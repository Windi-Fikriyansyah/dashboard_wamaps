<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Jobs\BroadcastMessage;

class WhatsAppBroadcastController extends Controller
{
    /**
     * Display the broadcast page.
     */
    public function index()
    {
        $leads = DB::table('leads')
            ->join('user_saved_leads', 'leads.id', '=', 'user_saved_leads.lead_id')
            ->where('user_saved_leads.user_id', Auth::id())
            ->select('leads.*', 'user_saved_leads.category as saved_category')
            ->get();

        $templates = DB::table('message_templates')
            ->where('user_id', Auth::id())
            ->get();

        $devices = DB::table('whatsapp_devices')
            ->where('user_id', Auth::id())
            ->get();

        $isRunning = \Illuminate\Support\Facades\Cache::has('broadcast_running_' . Auth::id());

        return view('whatsapp.broadcast', compact('leads', 'templates', 'devices', 'isRunning'));
    }

    /**
     * Start the broadcast by dispatching a background job.
     */
    public function send(Request $request)
    {
        $request->validate([
            'lead_ids' => 'required|array',
            'template_id' => 'required|integer',
            'device_id' => 'required|integer',
            'delay_min' => 'required|integer|min:1',
            'delay_max' => 'required|integer|min:1',
        ]);

        $device = DB::table('whatsapp_devices')
            ->where('id', $request->device_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$device) {
            return response()->json(['error' => 'Device tidak ditemukan.'], 404);
        }

        $template = DB::table('message_templates')
            ->where('id', $request->template_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$template) {
            return response()->json(['error' => 'Template tidak ditemukan.'], 404);
        }

        // Clear any existing stop signal before starting
        \Illuminate\Support\Facades\Cache::forget('stop_broadcast_' . Auth::id());

        // Dispatch background job
        BroadcastMessage::dispatch(
            $request->lead_ids,
            $template->content,
            $device->token,
            Auth::id(),
            (int) $request->delay_min,
            (int) $request->delay_max
        );

        return response()->json([
            'message' => 'Broadcast telah dimulai di latar belakang. Pesan akan dikirim secara bertahap dengan jeda aman untuk mencegah blokir.',
            'targets_count' => count($request->lead_ids),
            'mode' => 'background',
            'batch_info' => '20 pesan per batch, istirahat 15 menit antar batch, jeda acak ' . $request->delay_min . '-' . $request->delay_max . ' detik per pesan.'
        ]);
    }

    /**
     * Stop the active broadcast.
     */
    public function stop()
    {
        \Illuminate\Support\Facades\Cache::put('stop_broadcast_' . Auth::id(), true, now()->addHours(1));

        return response()->json([
            'message' => 'Sinyal berhenti dikirim. Pengiriman akan terhenti setelah pesan saat ini selesai diproses.'
        ]);
    }
}
