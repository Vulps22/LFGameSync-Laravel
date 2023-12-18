<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinkToken extends Model  
{

    use HasFactory;
    
    protected $fillable = [
        'token',
        'user_id',
        'expires',
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function isExpired()
    {
        return $this->expires->isPast();    
    }

}