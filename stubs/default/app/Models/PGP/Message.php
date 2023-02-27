<?php

namespace App\Models\PGP;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JTD420\PGP\Console\ModelTrait;

class Message extends Model
{
    use HasFactory, ModelTrait;

    protected $casts = [
        'receiver_id' => 'array',
    ];

    protected $fillable = [
        'sender_id', 'receiver_id', 'encrypted_subject', 'encrypted_message',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipients()
    {
        return $this->belongsToMany(User::class, 'message_recipients', 'message_id', 'recipient_id')->withPivot('encrypted_subject', 'encrypted_message');
    }

    public function replies()
    {
        return $this->hasMany(Reply::class, 'conversation_id')->orderBy('created_at', 'desc');
    }
}
