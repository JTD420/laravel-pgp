<?php

namespace App\Models\PGP;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JTD420\PGP\Console\ModelTrait;

class MessageRecipient extends Model
{
    use HasFactory, ModelTrait;

    //protected $table = 'message_recipients'; // When commented, it will be set by the ModelTrait and prepended with the config's table_prefix.

    protected $fillable = [
        'message_id', 'recipient_id', 'encrypted_subject', 'encrypted_message', 'is_read',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function replies()
    {
        return $this->hasMany(Reply::class, 'conversation_id')->orderBy('created_at', 'desc');
    }
}
