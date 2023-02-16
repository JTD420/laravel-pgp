<?php

namespace App\Models\PGP;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JTD420\PGP\Console\ModelTrait;

class Conversation extends Model
{
    use HasFactory, ModelTrait;

    //protected $table = 'conversations'; // When commented, it will be set by the ModelTrait and prepended with the config's table_prefix.

    protected $casts = [
        'recipient_id' => 'array',
    ];

    protected $fillable = [
        'sender_id', 'recipient_id', 'sent_subject', 'sent_message',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipients()
    {
        return $this->belongsToMany(User::class, config('PGP.table_prefix').'message_recipients', 'conversation_id', 'recipient_id')
            ->withPivot('encrypted_subject', 'encrypted_message');
    }

    public function replies()
    {
        return $this->hasMany(Reply::class, 'conversation_id')->orderBy('created_at', 'desc');
    }
}
