<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah.',
            ], 422);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth-token')->plainTextToken;

        Auth::login($user);

        return response()->json([
            'message' => 'Login successful',
            'token' => $token,
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function register(Request $request)
    {
        // Validasi tanpa 'unique:users,phone' agar 1 nomor WA bisa dipakai banyak akun demo
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string', 
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,guest,receptionist',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil',
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    // ─────────────────────────────────────────────────────────────
    // ALUR LUPA PASSWORD VIA WHATSAPP FONNTE (EMAIL BASED)
    // ─────────────────────────────────────────────────────────────

    public function checkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user->phone) {
            return response()->json(['message' => 'User ditemukan tetapi tidak memiliki nomor WhatsApp yang terdaftar.'], 422);
        }

        $phone = $user->phone;
        $maskedPhone = substr($phone, 0, 4) . '****' . substr($phone, -2);

        return response()->json([
            'message' => 'Email ditemukan',
            'phone' => $maskedPhone,
            'email' => $user->email // Kirim balik email untuk memastikan state terjaga
        ]);
    }

    public function forgotPassword(Request $request)
    {
        // Kunci utamanya disini: Cari berdasarkan EMAIL, bukan phone!
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        $otp = rand(100000, 999999);
        $user->otp = $otp;
        $user->otp_expires_at = Carbon::now()->addMinutes(10);
        $user->save();

        $pesanWa = "🏨 *GRAND LUXE / HOTEL 71*\n\nPermintaan reset password akun Anda sedang diproses.\n\nKode OTP Anda adalah: *{$otp}*\n\n_Kode ini rahasia dan hanya berlaku selama 10 Menit. Jangan berikan kepada siapapun._";
        
        $success = $this->sendWhatsApp($user->phone, $pesanWa);
        
        if (!$success) {
            return response()->json(['message' => 'Gagal mengirim pesan WhatsApp. Pastikan layanan Fonnte aktif.'], 500);
        }

        return response()->json(['message' => 'OTP telah dikirim ke nomor WhatsApp Anda']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
                    ->where('otp', $request->otp)
                    ->where('otp_expires_at', '>', Carbon::now())
                    ->first();

        if (!$user) {
            return response()->json(['message' => 'Kode OTP tidak valid atau sudah kedaluwarsa.'], 422);
        }

        // Token konfirmasi untuk ganti password (kombinasi email + otp)
        $verifyToken = base64_encode($user->email . '|' . $user->otp);

        return response()->json(['message' => 'OTP valid', 'token' => $verifyToken]);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $decoded = base64_decode($request->token);
        $parts = explode('|', $decoded);

        // Mencegah error jika token corrupt
        if (count($parts) !== 2) {
            return response()->json(['message' => 'Token reset tidak valid.'], 422);
        }

        list($tokenEmail, $otp) = $parts;

        if ($tokenEmail !== $request->email) {
            return response()->json(['message' => 'Token tidak sesuai dengan sesi email Anda.'], 422);
        }

        $user = User::where('email', $request->email)->where('otp', $otp)->first();
        if (!$user) {
            return response()->json(['message' => 'Sesi reset password tidak valid atau sudah digunakan.'], 422);
        }

        $user->password = Hash::make($request->password);
        $user->otp = null;
        $user->otp_expires_at = null;
        $user->save();

        return response()->json(['message' => 'Password berhasil diperbarui. Silakan login dengan password baru Anda.']);
    }

    // Fungsi helper kirim WA via Fonnte
    private function sendWhatsApp($to, $message)
    {
        try {
            // Membaca token murni dari file .env
            $token = env('FONNTE_TOKEN'); 
            
            if (str_starts_with($to, '0')) {
                $to = '62' . substr($to, 1);
            }
            
            $response = Http::withoutVerifying()->withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $to,
                'message' => $message,
                'countryCode' => '62', // Optional safety untuk nomor ID
            ]);
            
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("Fonnte Error: " . $e->getMessage());
            return false;
        }
    }
}