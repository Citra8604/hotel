<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Reservation;
use App\Models\Room;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        // Statistik ringkas untuk dashboard - data real dari database
        $activeReservations = Reservation::whereIn('status', ['pending', 'verified', 'checked_in'])->count();
        $totalRevenue = Reservation::where('status', 'verified')->sum('total_price');
        $recentReservations = Reservation::with(['user', 'roomType'])
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'stats' => [
                'total_users' => User::count(),
                'active_reservations' => $activeReservations,
                'total_revenue' => $totalRevenue,
            ],
            'recent_reservations' => $recentReservations
        ]);
    }

    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('admin.users', compact('users'));
    }

    public function rooms()
    {
        $rooms = Room::with('roomType')->orderBy('room_number')->get();
        return view('admin.rooms', compact('rooms'));
    }
}