<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fistname',
        'sirname',
        'phone',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */

    protected $casts = [
        'phone_verified_at' => 'datetime',
        'phone_code_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'dob' => 'date:Y-m-d',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    protected $dates = ['last_login', 'phone_verified_at', 'email_verified_at', 'phone_code_at'];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($user) {
            if ($user->forceDeleting)
                $user->deleteAvatar();
        });

        self::creating(function ($user) {
            $user->phone_verified_at = now();
            // $user->code_verified_at = now();
        });
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }

    public function vikoba()
    {
        return $this->belongsToMany(Vikoba::class, Member::class);
    }
    public function setGenderAttribute($value)
    {
        $gender = strtolower($value);

        $this->attributes['gender'] = in_array($gender, ['male', 'female', 'notset'])
            ? $gender
            : null;
    }
/**
     * Define appended accessor
     *
     * @return string
     */
    public function getFullNameAttribute()
    {
        return $this->firstname . ' ' . $this->sirname;
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
