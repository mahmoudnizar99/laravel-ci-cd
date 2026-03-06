<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable ;

    protected $fillable = [
        'name',
        'age',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function fixedOrders()
    {
        return $this->hasMany(FixedOrder::class);
    }

    public function jobApplications()
    {
        return $this->hasMany(JobApplication::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function productOrders()
    {
        return $this->hasMany(ProductOrder::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function comments()
    {
        return $this->hasMany(PostComment::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isCompany()
    {
        return $this->role === 'company';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmailNotification);
    }
}
