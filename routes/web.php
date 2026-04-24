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

    // WhatsApp Device Routes
    Route::get('/whatsapp/devices', [\App\Http\Controllers\WhatsAppController::class, 'index'])->name('whatsapp.devices');
    Route::post('/whatsapp/devices', [\App\Http\Controllers\WhatsAppController::class, 'store'])->name('whatsapp.devices.store');
    Route::delete('/whatsapp/devices/{id}', [\App\Http\Controllers\WhatsAppController::class, 'destroy'])->name('whatsapp.devices.destroy');
    Route::get('/whatsapp/devices/{id}/qr', [\App\Http\Controllers\WhatsAppController::class, 'getQr'])->name('whatsapp.devices.qr');
    Route::post('/whatsapp/devices/{id}/reconnect', [\App\Http\Controllers\WhatsAppController::class, 'reconnect'])->name('whatsapp.devices.reconnect');
    Route::post('/whatsapp/devices/{id}/disconnect', [\App\Http\Controllers\WhatsAppController::class, 'disconnect'])->name('whatsapp.devices.disconnect');
    Route::post('/whatsapp/devices/{id}/status', [\App\Http\Controllers\WhatsAppController::class, 'status'])->name('whatsapp.devices.status');
});


require __DIR__ . '/auth.php';
