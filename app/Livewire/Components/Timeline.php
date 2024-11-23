<?php

namespace App\Livewire\Components;

use App\Models\Tweet;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

class Timeline extends Component
{
    public Collection $tweets;
    public array $chunks = [];
    public int $page = 1;
    public int $chunkSize = 10;

    public function mount(): void
    {
        $this->loadInitialChunks();
    }

    private function loadInitialChunks(): void
    {
        $user = auth()->user();

        // Get the IDs of the tweets based on the criteria
        $allTweetIds = Tweet::where(function ($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orWhereIn('id', $user->likes()->pluck('tweet_id'))
                ->orWhereIn('user_id', $user->following()->pluck('id'))
                ->orWhereHas('retweets', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                });
        })->latest()->pluck('id');

        $this->chunks = $allTweetIds->chunk($this->chunkSize)->toArray();

        if (!empty($this->chunks)) {
            $this->tweets = Tweet::whereIn('id', $this->chunks[0])->latest()->get();
        } else {
            $this->tweets = collect();
        }
    }

    public function hasMorePages(): bool
    {
        return $this->page < count($this->chunks);
    }

    public function loadMore(): void
    {
        if (!$this->hasMorePages()) {
            return;
        }

        $this->page++;
        $nextChunkIds = $this->chunks[$this->page - 1];
        $moreTweets = Tweet::whereIn('id', $nextChunkIds)->latest()->get();
        $this->tweets = $this->tweets->merge($moreTweets);
    }

    #[On('echo:tweets,TweetWasCreated')]
    public function listenForTweet($tweet): void
    {
        $tweet = Tweet::find($tweet['id']);
        if ($tweet) {
            $this->tweets->prepend($tweet);
        }
    }

    #[On('echo:tweets,TweetWasDeleted')]
    public function listenForDeletedTweets($tweet): void
    {
        $tweet = Tweet::find($tweet['id']);
        if ($tweet) {
            $this->tweets = $this->tweets->reject(function ($t) use ($tweet) {
                return $t->id === $tweet->id;
            });
        }
    }

    #[On('addTweet')]
    public function addTweet($tweetId): void
    {
        $tweet = Tweet::find($tweetId);
        if ($tweet) {
            $this->tweets->prepend($tweet);
        }
    }

    public function render(): View
    {
        return view('livewire.components.timeline');
    }
}