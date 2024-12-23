<?php

namespace App\Models;

use App\Entities\EntityExtractor;
use App\Models\Traits\HasRetweets;
use App\TweetType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Tweet extends Model implements HasMedia
{
    use InteractsWithMedia;
    use HasFactory;
    use SoftDeletes;
    use HasRetweets;

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($tweet) {
            $tweet->uuid = Str::uuid();
        });

        static::created(function ($tweet) {
            if ($tweet->type !== TweetType::RETWEET) {
                $tweet->entities()->createMany(
                    (new EntityExtractor($tweet->body))->getAllEntities()
                );
            }
        });
    }

    public function getContentWithLinksAttribute(): string
    {
        $extractor = new \App\Entities\EntityExtractor($this->body);
        return $extractor->renderEntitiesWithLinks();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(Like::class);
    }

    public function entities(): HasMany
    {
        return $this->hasMany(Entity::class);
    }
}
