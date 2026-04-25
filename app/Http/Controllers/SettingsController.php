<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Display the settings page.
     */
    public function index()
    {
        $user = Auth::user();
        return view('settings.index', compact('user'));
    }

    /**
     * Update user profile and API settings.
     */
    public function update(Request $request)
    {
        $userId = Auth::id();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),
            ],
            'password' => 'nullable|string|min:8|confirmed',
            'fonnte_token' => 'nullable|string',
            'search_api_key' => 'nullable|string',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'fonnte_token' => $request->fonnte_token,
            'search_api_key' => $request->search_api_key,
            'updated_at' => now(),
        ];

        // Update password if provided
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            \Illuminate\Support\Facades\Log::info("User ID $userId updated their password.");
        }

        $updated = DB::table('users')->where('id', $userId)->update($data);

        \Illuminate\Support\Facades\Log::info("User ID $userId updated their settings. Row affected: $updated");

        return response()->json(['message' => 'Pengaturan berhasil disimpan!']);
    }
}
