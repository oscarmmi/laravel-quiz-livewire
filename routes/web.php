<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\HomeController;
use App\Livewire\Leaderboard;

Route::view('/', 'welcome');

Route::get('dashboard', [HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('leaderboard', Leaderboard::class)
    ->middleware(['auth'])
    ->name('leaderboard');

Route::get('admin/users', \App\Livewire\Admin\ManageAdmins::class)
    ->middleware(['auth'])
    ->name('admin.users');

Route::get('admin/questions', \App\Livewire\Admin\ManageQuestions::class)
    ->middleware(['auth'])
    ->name('admin.questions');

Route::get('admin/quizzes', \App\Livewire\Admin\ManageQuizzes::class)
    ->middleware(['auth'])
    ->name('admin.quizzes');

Route::get('admin/tests', \App\Livewire\Admin\ManageTests::class)
    ->middleware(['auth'])
    ->name('admin.tests');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/auth/{provider}/redirect', [SocialLoginController::class, 'redirect'])
    ->name('social.redirect');

Route::get('/auth/{provider}/callback', [SocialLoginController::class, 'callback'])
    ->name('social.callback');

require __DIR__.'/auth.php';
