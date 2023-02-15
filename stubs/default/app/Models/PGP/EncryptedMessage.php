<?php

namespace App\Models\PGP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JTD420\PGP\Console\ModelTrait;

class EncryptedMessage extends Model
{
    use HasFactory, ModelTrait;

    protected $fillable = [
        'conversation_id', 'recipient_id', 'encrypted_subject', 'encrypted_message', 'is_read'
    ];

    public function messageRecipient()
    {
        return $this->belongsTo(MessageRecipient::class, 'message_recipient_id');
    }

}
