<?php

use App\Http\Controllers\BlogController;
use Illuminate\Support\Facades\Route;

Route::get('/', [BlogController::class, 'index'])->name('home');

// Kept last: a bare "/{post:slug}" segment would otherwise shadow any route
// defined after it (the admin panel lives under its own /admin prefix, so
// no conflict there, but this stays last on principle).
Route::get('/{post:slug}', [BlogController::class, 'show'])->name('posts.show');
