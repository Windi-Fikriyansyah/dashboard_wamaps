<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppHistoryController extends Controller
{
    /**
     * Display the message history page.
     */
    public function index()
    {
        $userId = Auth::id();
        $histories = DB::table('message_histories')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        Log::info("History Page accessed by User ID: $userId. Records found: " . $histories->count());

        return view('whatsapp.history', compact('histories'));
    }

    /**
     * Refresh message statuses from Fonnte API.
     */
    public function refresh()
    {
        $user = Auth::user();
        
        // Get user's device
        $device = DB::table('whatsapp_devices')
            ->where('user_id', Auth::id())
            ->first();

        if (!$device) {
            return response()->json(['error' => 'Device tidak ditemukan. Silakan tambahkan device terlebih dahulu.'], 400);
        }

        // Get messages that are not in final state
        $nonFinalStatuses = ['pending', 'waiting', 'processing', 'process', 'queued'];
        $pendingMessages = DB::table('message_histories')
            ->where('user_id', Auth::id())
            ->whereIn('status', $nonFinalStatuses)
            ->get();

        $updatedCount = 0;

        foreach ($pendingMessages as $msg) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => $device->token
                ])->asForm()->post('https://api.fonnte.com/status', [
                    'id' => $msg->id
                ]);

                $result = $response->json();

                if ($result['status'] ?? false) {
                    $newStatus = $result['message_status'] ?? $msg->status;
                    if ($newStatus != $msg->status) {
                        DB::table('message_histories')
                            ->where('id', $msg->id)
                            ->update([
                                'status' => $newStatus,
                                'updated_at' => now()
                            ]);
                        $updatedCount++;
                    }
                }
            } catch (\Exception $e) {
                Log::error("Failed to refresh status for message {$msg->id}: " . $e->getMessage());
            }
        }

        return response()->json([
            'message' => "Refresh selesai. $updatedCount status pesan diperbarui.",
            'updated_count' => $updatedCount
        ]);
    }

    /**
     * Fonnte Webhook to receive real-time status updates.
     */
    public function webhook(Request $request)
    {
        // Fonnte sends data as POST, could be JSON or Form Data
        $payload = $request->all();
        
        $msgId = $payload['id'] ?? null;
        $status = $payload['status'] ?? null;
        
        if ($msgId) {
            DB::table('message_histories')
                ->where('id', (string) $msgId)
                ->update([
                    'status' => $status,
                    'updated_at' => now()
                ]);
        }

        return response()->json(['status' => 'ok']);
    }
}
