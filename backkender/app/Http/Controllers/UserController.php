<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();
        
        if ($request->has('role')) {
            $roleMap = ['guest' => 'tamu', 'receptionist' => 'resepsionis', 'admin' => 'admin'];
            $query->where('role', $roleMap[$request->role] ?? $request->role);
        }

        $list = $query->get();
        $list->map(function($u) {
            $u->phone = $u->no_telp; // Dual binding pengunci error
            return $u;
        });

        return response()->json([
            'success' => true,
            'data' => $list
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $roleInput = $request->role;
        $roleMap = ['admin' => 'admin', 'receptionist' => 'resepsionis', 'guest' => 'tamu'];
        $finalRole = $roleMap[$roleInput] ?? $roleInput;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'no_telp' => $request->phone ?? $request->no_telp ?? '0812345678',
            'password' => Hash::make($request->password),
            'role' => $finalRole,
        ]);

        return response()->json($user, 201);
    }

    public function show($id)
    {
        return response()->json(User::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        if($request->has('role')) {
            $roleMap = ['admin' => 'admin', 'receptionist' => 'resepsionis', 'guest' => 'tamu'];
            $user->role = $roleMap[$request->role] ?? $request->role;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        return response()->json($user);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['success' => true]);
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        $user = auth()->user();
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Password saat ini salah'], 400);
        }

        User::where('id', $user->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json(['message' => 'Password berhasil diubah']);
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        User::where('id', $user->id)->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);
        return response()->json(['message' => 'Profil berhasil diperbarui']);
    }
}