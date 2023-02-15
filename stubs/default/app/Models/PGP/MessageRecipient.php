<?php

namespace App\Models\PGP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JTD420\PGP\Console\ModelTrait;

class MessageRecipient extends Model
{
    use HasFactory, ModelTrait;

    protected $fillable = [
        'conversation_id', 'recipient_id', 'encrypted_subject', 'encrypted_message', 'is_read'
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id', 'id');
    }

    public function encryptedMessage()
    {
        return $this->hasOne(EncryptedMessage::class, 'message_recipient_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function replies()
    {
        return $this->hasMany(Reply::class, 'conversation_id')->orderBy('created_at', 'desc');
    }
}
