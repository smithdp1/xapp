<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Define the relationship for the tweets created by the user
    public function tweets()
    {
        return $this->hasMany(Tweet::class);
    }

    // Define the relationship for the tweets liked by the user
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // Define the relationship for the users followed by the current user
    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'user_id', 'follower_id');
    }

    // Define the relationship for the retweets made by the user
    public function retweets()
    {
        return $this->hasMany(Retweet::class);
    }
}