<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WhatsAppTemplateController extends Controller
{
    /**
     * Display a listing of the message templates.
     */
    public function index()
    {
        $templates = DB::table('message_templates')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('whatsapp.templates', compact('templates'));
    }

    /**
     * Store a newly created message template in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $templateId = DB::table('message_templates')->insertGetId([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'content' => $request->content,
            'created_at' => now(),
        ]);

        $template = DB::table('message_templates')
            ->where('id', $templateId)
            ->where('user_id', Auth::id())
            ->first();

        return response()->json($template);
    }

    /**
     * Update the specified message template in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        DB::table('message_templates')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->update([
                'name' => $request->name,
                'content' => $request->content,
            ]);

        $template = DB::table('message_templates')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        return response()->json($template);
    }

    /**
     * Remove the specified message template from storage.
     */
    public function destroy($id)
    {
        $deleted = DB::table('message_templates')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->delete();

        if (!$deleted) {
            return response()->json(['error' => 'Template not found or unauthorized.'], 404);
        }

        return response()->json(['message' => 'Template deleted successfully.']);
    }

    /**
     * Get a specific template as JSON.
     */
    public function show($id)
    {
        $template = DB::table('message_templates')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$template) {
            return response()->json(['error' => 'Template not found.'], 404);
        }

        return response()->json($template);
    }
}
