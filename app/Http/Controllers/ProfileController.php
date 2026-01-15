<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    

    /**
     * Tampilkan halaman profil
     */
    public function edit(Request $request): View
    {
        return view('profile.index', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update data profil (nama)
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $request->user()->update([
            'name' => $request->name,
        ]);

        return back()->with('success', 'Profil berhasil diperbarui');
    }

    /**
     * Update password
     */
    
    public function updatePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required',
        'password' => 'required|min:8|confirmed',
    ]);

    $user = $request->user(); // ← pakai ini saja

    if (!Hash::check($request->current_password, $user->password)) {
        return back()->withErrors([
            'current_password' => 'Password lama tidak sesuai',
        ]);
    }

    // ⛔ JANGAN Hash::make kalau pakai cast hashed
    $user->password = $request->password;
    $user->save();

    // 🔥 WAJIB LOGOUT
    Auth::logout();

    // 🔥 INVALIDATE SESSION
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login')
        ->with('status', 'Password berhasil diperbarui, silakan login kembali');
}
}
