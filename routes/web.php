<?php

use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\HomeController;
use App\Livewire\Admin\ManageAdmins;
use App\Livewire\Admin\ManageQuestions;
use App\Livewire\Admin\ManageQuizzes;
use App\Livewire\Admin\ManageTests;
use App\Livewire\Leaderboard;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('dashboard', [HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('leaderboard', Leaderboard::class)
    ->middleware(['auth'])
    ->name('leaderboard');

Route::get('admin/users', ManageAdmins::class)
    ->middleware(['auth'])
    ->name('admin.users');

Route::get('admin/questions', ManageQuestions::class)
    ->middleware(['auth'])
    ->name('admin.questions');

Route::get('admin/quizzes', ManageQuizzes::class)
    ->middleware(['auth'])
    ->name('admin.quizzes');

Route::get('admin/tests', ManageTests::class)
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
