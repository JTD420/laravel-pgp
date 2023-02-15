<?php

namespace App\Models\PGP;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JTD420\PGP\Console\ModelTrait;

class ReplyRecipient extends Model
{
    use HasFactory, ModelTrait;

    protected $fillable = [
        'conversation_id', 'recipient_id', 'recipient_reply_id',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'recipient_id');
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
