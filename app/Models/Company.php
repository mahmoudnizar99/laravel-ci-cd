<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'location',
        'type',
        'is_active',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(CompanyAnswer::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
