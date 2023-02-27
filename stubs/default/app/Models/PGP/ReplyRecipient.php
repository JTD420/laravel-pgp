<?php

namespace App\Models\PGP;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JTD420\PGP\Console\ModelTrait;

class ReplyRecipient extends Model
{
    use HasFactory, ModelTrait;

    //protected $table = 'reply_recipients'; // When commented, it will be set by the ModelTrait and prepended with the config's table_prefix.

    protected $fillable = [
        'reply_id', 'recipient_id', 'encrypted_message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function reply()
    {
        return $this->belongsTo(Reply::class);
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
