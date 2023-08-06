<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserToken extends Model
{
    use HasFactory;

    protected $primaryKey = 'token'; // Use 'token' as the primary key

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
