<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tweet extends Model
{
    use HasFactory;

    // Define the relationship for retweets
    public function retweets()
    {
        return $this->hasMany(Retweet::class);
    }

    // Define the relationship for likes
    public function likes()
    {
        return $this->hasMany(Like::class);
    }

    // Define the relationship for the user who created the tweet
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}