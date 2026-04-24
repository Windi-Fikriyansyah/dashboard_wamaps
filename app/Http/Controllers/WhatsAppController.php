<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{
    protected $fonnteApiUrl = 'https://api.fonnte.com';

    /**
     * Get user's Fonnte token (from profile or global env)
     */
    private function getFonnteToken()
    {
        $user = Auth::user();
        $token = $user->fonnte_token ?? env('FONNTE_TOKEN');

        if (!$token || $token == 'REPLACE_WITH_YOUR_FONNTE_TOKEN') {
            return null;
        }

        return $token;
    }

    /**
     * Normalize phone number to E.164 without '+'
     */
    private function normalizePhone($phone)
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleaned, '08')) {
            return '628' . substr($cleaned, 2);
        }
        return $cleaned;
    }

    /**
     * List all devices
     */
    public function index()
    {
        $devices = DB::table('whatsapp_devices')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('whatsapp.devices', compact('devices'));
    }

    /**
     * Add new device to Fonnte and Database
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'device' => 'required|string',
        ]);

        $token = $this->getFonnteToken();
        if (!$token) {
            return response()->json(['error' => 'Fonnte Token belum diisi di profil Anda.'], 400);
        }

        $cleanDevice = $this->normalizePhone($request->device);

        try {
            $response = Http::withHeaders([
                'Authorization' => $token
            ])->asForm()->post($this->fonnteApiUrl . '/add-device', [
                'name' => $request->name,
                'device' => $cleanDevice,
                'autoread' => 'false',
                'personal' => 'false',
                'group' => 'false'
            ]);

            $result = $response->json();

            if (!($result['status'] ?? false)) {
                return response()->json(['error' => 'Fonnte error: ' . ($result['reason'] ?? 'Gagal menambahkan device.')], 400);
            }

            $deviceId = DB::table('whatsapp_devices')->insertGetId([
                'user_id' => Auth::id(),
                'name' => $request->name,
                'device_number' => $cleanDevice,
                'token' => $result['token'],
                'status' => 'disconnected',
                'created_at' => now()
            ]);

            $device = DB::table('whatsapp_devices')->where('id', $deviceId)->first();

            // Auto-update webhook
            $backendUrl = url('/');
            Http::withHeaders([
                'Authorization' => $result['token']
            ])->asForm()->post($this->fonnteApiUrl . '/update-device', [
                'name' => $request->name,
                'webhook' => $backendUrl . '/api/whatsapp/webhook',
                'webhookconnect' => $backendUrl . '/api/whatsapp/webhook-connect',
                'webhookstatus' => $backendUrl . '/api/whatsapp/webhook',
            ]);

            return response()->json($device);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Get QR Code from Fonnte
     */
    public function getQr($id)
    {
        $device = DB::table('whatsapp_devices')->where('id', $id)->where('user_id', Auth::id())->first();
        if (!$device) abort(404);

        try {
            $response = Http::withHeaders([
                'Authorization' => $device->token
            ])->asForm()->post($this->fonnteApiUrl . '/qr');

            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Disconnect device
     */
    public function disconnect($id)
    {
        $device = DB::table('whatsapp_devices')->where('id', $id)->where('user_id', Auth::id())->first();
        if (!$device) abort(404);

        try {
            $response = Http::withHeaders([
                'Authorization' => $device->token
            ])->post($this->fonnteApiUrl . '/disconnect');

            $result = $response->json();
            if (($result['status'] ?? false) || ($result['detail'] ?? '') == 'device already disconnected') {
                DB::table('whatsapp_devices')->where('id', $id)->update(['status' => 'disconnected']);
            }

            return $result;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Reconnect device
     */
    public function reconnect($id)
    {
        $device = DB::table('whatsapp_devices')->where('id', $id)->where('user_id', Auth::id())->first();
        if (!$device) abort(404);

        try {
            $response = Http::withHeaders([
                'Authorization' => $device->token
            ])->post($this->fonnteApiUrl . '/reconnect');

            return $response->json();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete device from local database
     */
    public function destroy($id)
    {
        $deleted = DB::table('whatsapp_devices')->where('id', $id)->where('user_id', Auth::id())->delete();
        
        if (!$deleted) abort(404);

        return response()->json(['message' => 'Device deleted successfully.']);
    }

    /**
     * Refresh device status
     */
    public function status($id)
    {
        $device = DB::table('whatsapp_devices')->where('id', $id)->where('user_id', Auth::id())->first();
        if (!$device) abort(404);

        try {
            $response = Http::withHeaders([
                'Authorization' => $device->token
            ])->post($this->fonnteApiUrl . '/device');

            $result = $response->json();
            if ($result['status'] ?? false) {
                $newStatus = ($result['device_status'] ?? '') == 'connect' ? 'connected' : 'disconnected';
                DB::table('whatsapp_devices')->where('id', $id)->update(['status' => $newStatus]);
            }

            return $result;
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
