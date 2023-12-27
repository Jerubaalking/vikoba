<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Member extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function vikoba()
    {
        return $this->belongsTo(Vikoba::class, 'vikoba_id');
    }
}
