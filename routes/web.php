<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Leads Routes
    Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
    Route::post('/leads/search', [LeadController::class, 'search'])->name('leads.search');
    Route::get('/leads/data', [LeadController::class, 'data'])->name('leads.data');
    Route::post('/leads/save', [LeadController::class, 'save'])->name('leads.save');
    Route::post('/leads/save-batch', [LeadController::class, 'saveBatch'])->name('leads.save-batch');
});


require __DIR__ . '/auth.php';
