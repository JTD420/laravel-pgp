<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JTD420\PGP\Console\ModelTrait;

class Key extends Model
{
    use HasFactory, ModelTrait;

    //protected $table = 'keys'; // When commented, it will be set by the ModelTrait and use the config's table_prefix.

    protected $fillable = [
        'user_id', 'public_key', 'private_key',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
