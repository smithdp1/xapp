<?php

use App\Livewire\Pages\Home;
use App\Livewire\Pages\Notifications;
use App\Livewire\Pages\Profile;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home')->middleware('auth');
Route::get('/notifications', Notifications::class)->name('notifications')->middleware('auth');
Route::get('/profile/{user:username}', Profile::class)->name('profile.show')->middleware('auth');