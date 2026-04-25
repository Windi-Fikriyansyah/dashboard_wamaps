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
    Route::delete('/leads/{id}', [LeadController::class, 'destroy'])->name('leads.destroy');
    Route::post('/leads/delete-batch', [LeadController::class, 'destroyBatch'])->name('leads.delete-batch');

    // WhatsApp Device Routes
    Route::get('/whatsapp/devices', [\App\Http\Controllers\WhatsAppController::class, 'index'])->name('whatsapp.devices');
    Route::post('/whatsapp/devices', [\App\Http\Controllers\WhatsAppController::class, 'store'])->name('whatsapp.devices.store');
    Route::delete('/whatsapp/devices/{id}', [\App\Http\Controllers\WhatsAppController::class, 'destroy'])->name('whatsapp.devices.destroy');
    Route::get('/whatsapp/devices/{id}/qr', [\App\Http\Controllers\WhatsAppController::class, 'getQr'])->name('whatsapp.devices.qr');
    Route::post('/whatsapp/devices/{id}/reconnect', [\App\Http\Controllers\WhatsAppController::class, 'reconnect'])->name('whatsapp.devices.reconnect');
    Route::post('/whatsapp/devices/{id}/disconnect', [\App\Http\Controllers\WhatsAppController::class, 'disconnect'])->name('whatsapp.devices.disconnect');
    Route::post('/whatsapp/devices/{id}/status', [\App\Http\Controllers\WhatsAppController::class, 'status'])->name('whatsapp.devices.status');

    // WhatsApp Template Routes
    Route::get('/whatsapp/templates', [\App\Http\Controllers\WhatsAppTemplateController::class, 'index'])->name('whatsapp.templates');
    Route::post('/whatsapp/templates', [\App\Http\Controllers\WhatsAppTemplateController::class, 'store'])->name('whatsapp.templates.store');
    Route::get('/whatsapp/templates/{id}', [\App\Http\Controllers\WhatsAppTemplateController::class, 'show'])->name('whatsapp.templates.show');
    Route::put('/whatsapp/templates/{id}', [\App\Http\Controllers\WhatsAppTemplateController::class, 'update'])->name('whatsapp.templates.update');
    Route::delete('/whatsapp/templates/{id}', [\App\Http\Controllers\WhatsAppTemplateController::class, 'destroy'])->name('whatsapp.templates.destroy');

    // WhatsApp Broadcast Routes
    Route::get('/whatsapp/broadcast', [\App\Http\Controllers\WhatsAppBroadcastController::class, 'index'])->name('whatsapp.broadcast');
    Route::post('/whatsapp/broadcast', [\App\Http\Controllers\WhatsAppBroadcastController::class, 'send'])->name('whatsapp.broadcast.send');
    Route::post('/whatsapp/broadcast/stop', [\App\Http\Controllers\WhatsAppBroadcastController::class, 'stop'])->name('whatsapp.broadcast.stop');

    // WhatsApp History Routes
    Route::get('/whatsapp/history', [\App\Http\Controllers\WhatsAppHistoryController::class, 'index'])->name('whatsapp.history');
    Route::post('/whatsapp/history/refresh', [\App\Http\Controllers\WhatsAppHistoryController::class, 'refresh'])->name('whatsapp.history.refresh');

    // Settings Routes
    Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [\App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');

    // Extras Routes
    Route::get('/bonus', [\App\Http\Controllers\ExtrasController::class, 'bonus'])->name('bonus');
    Route::get('/extension', [\App\Http\Controllers\ExtrasController::class, 'extension'])->name('extension');
    Route::get('/software', [\App\Http\Controllers\ExtrasController::class, 'software'])->name('software');
});

// WhatsApp Webhook (Outside Auth)
Route::post('/whatsapp/webhook', [\App\Http\Controllers\WhatsAppHistoryController::class, 'webhook'])->name('whatsapp.webhook');


require __DIR__ . '/auth.php';
