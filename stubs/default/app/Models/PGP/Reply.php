<?php

namespace App\Models\PGP;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JTD420\PGP\Console\ModelTrait;

class Reply extends Model
{
    use HasFactory, ModelTrait;

    //protected $table = 'replies'; // When commented, it will be set by the ModelTrait and prepended with the config's table_prefix.
    protected $fillable = [
        'conversation_id', 'sender_id', 'encrypted_message',
    ];

    public function conversation()
    {
        return $this->belongsTo(Message::class, 'conversation_id');
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
