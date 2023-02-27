<?php

namespace App\Http\Controllers\PGP;

use OpenPGP;
use OpenPGP_CompressedDataPacket;
use OpenPGP_Crypt_RSA;
use OpenPGP_Crypt_Symmetric;
use OpenPGP_LiteralDataPacket;
use OpenPGP_Message;
use OpenPGP_PublicKeyPacket;
use OpenPGP_SecretKeyPacket;
use OpenPGP_SignaturePacket;
use OpenPGP_SignaturePacket_IssuerPacket;
use OpenPGP_SignaturePacket_KeyFlagsPacket;
use OpenPGP_UserIDPacket;
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\RSA\Formats\Keys\PKCS1;

class PGPController extends Controller
{
    public function generate_keypair($name, $email, $passphrase, $key_length = 2048)
    {

        // Generate a key pair
        $privateKey = RSA::createKey($key_length);
        $privateKeyComponents = PKCS1::load($privateKey->toString('PKCS1'));

        $secretKeyPacket = new OpenPGP_SecretKeyPacket(array(
            'n' => $privateKeyComponents["modulus"]->toBytes(),
            'e' => $privateKeyComponents["publicExponent"]->toBytes(),
            'd' => $privateKeyComponents["privateExponent"]->toBytes(),
            'p' => $privateKeyComponents["primes"][1]->toBytes(),
            'q' => $privateKeyComponents["primes"][2]->toBytes(),
            'u' => $privateKeyComponents["coefficients"][2]->toBytes()
        ));

        // Assemble packets for the private key
        $packets = array($secretKeyPacket);

        $wkey = new OpenPGP_Crypt_RSA($secretKeyPacket);
        $fingerprint = $wkey->key()->fingerprint;
        $key = $wkey->private_key();
        $key = $key->withHash('sha256');
        $keyid = substr($fingerprint, -16);

        // Add a user ID packet
        $uid = new OpenPGP_UserIDPacket("$name <$email>");
        $packets[] = $uid;

        // Add a signature packet to certify the binding between the user ID and the key
        $sig = new OpenPGP_SignaturePacket(new OpenPGP_Message(array($secretKeyPacket, $uid)), 'RSA', 'SHA256');
        $sig->signature_type = 0x13;
        $sig->hashed_subpackets[] = new OpenPGP_SignaturePacket_KeyFlagsPacket(array(0x01 | 0x02 | 0x04)); // Certify + sign + encrypt bits
        $sig->hashed_subpackets[] = new OpenPGP_SignaturePacket_IssuerPacket($keyid);
        $m = $wkey->sign_key_userid(array($secretKeyPacket, $uid, $sig));

        // Append the signature to the private key packets
        $packets[] = $m->packets[2];

        // Assemble packets for the public key
        $publicPackets = array(new OpenPGP_PublicKeyPacket($secretKeyPacket));
        $publicPackets[] = $uid;
        $publicPackets[] = $sig;

        // Encrypt the private key with a passphrase
        $encryptedSecretKeyPacket = OpenPGP_Crypt_Symmetric::encryptSecretKey($passphrase, $secretKeyPacket);

        // Assemble the private key message
        $privateMessage = new OpenPGP_Message($packets);
        $privateMessage[0] = $encryptedSecretKeyPacket;

        // Enarmor the private key message
        $privateEnarmorKey = OpenPGP::enarmor($privateMessage->to_bytes(), "PGP PRIVATE KEY BLOCK");

        // Assemble the public key message
        $publicMessage = new OpenPGP_Message($publicPackets);

        // Enarmor the public key message
        $publicEnarmorKey = OpenPGP::enarmor($publicMessage->to_bytes(), "PGP PUBLIC KEY BLOCK");

        return array(
            'public_key' => $publicEnarmorKey,
            'private_key' => $privateEnarmorKey
        );
    }

    public function encrypt($public_key, $message)
    {
        $recipientPublicKey = OpenPGP_Message::parse(OpenPGP::unarmor($public_key, 'PGP PUBLIC KEY BLOCK'));
        $data = new OpenPGP_LiteralDataPacket($message, ['format' => 'u']);
        $compressed = new OpenPGP_CompressedDataPacket($data);
        $encrypted = OpenPGP_Crypt_Symmetric::encrypt($recipientPublicKey, new OpenPGP_Message([$compressed]));
        return OpenPGP::enarmor($encrypted->to_bytes(), 'PGP MESSAGE');
    }

    public function decrypt($private_key, $encrypted_message, $passphrase)
    {
        try {
            $encryptedPrivateKey = OpenPGP_Message::parse(OpenPGP::unarmor($private_key, 'PGP PRIVATE KEY BLOCK'));
            // Try each secret key packet
            foreach ($encryptedPrivateKey as $p) {
                if (!($p instanceof OpenPGP_SecretKeyPacket)) continue;
                $keyd = OpenPGP_Crypt_Symmetric::decryptSecretKey($passphrase, $p);
                $msg = OpenPGP_Message::parse(OpenPGP::unarmor($encrypted_message, 'PGP MESSAGE'));
                $decryptor = new OpenPGP_Crypt_RSA($keyd);
                $decrypted = $decryptor->decrypt($msg);
                $data_packet = $decrypted->packets[0]->data->packets[0];
                return $data_packet->data;
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unfortunately we were unable to decrypt your message at this time. Please verify you are using the correct password and try again.'], 403);
        }
    }
}
