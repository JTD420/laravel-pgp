<?php

namespace App\Models\PGP;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JTD420\PGP\Console\ModelTrait;

class Reply extends Model
{
    use HasFactory, ModelTrait;

    protected $fillable = [
        'conversation_id', 'sender_id', 'encrypted_message',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipients()
    {
        return $this->belongsToMany(User::class, 'reply_recipients', 'reply_id', 'recipient_id')->withPivot('encrypted_message');
    }

    public function latestReplyTimestamp()
    {
        return $this->latest()->pluck('created_at')->first();
    }
}
