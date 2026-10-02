<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\RoomType;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservationController extends Controller
{
    // ─────────────────────────────────────────
    // PUBLIC: Search Available Room Types
    // ─────────────────────────────────────────
    public function searchRooms(Request $request)
    {
        $request->validate([
            'check_in'  => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $checkIn  = $request->check_in;
        $checkOut = $request->check_out;

        $roomTypes = RoomType::all();

        $availableRoomTypes = $roomTypes->map(function ($type) use ($checkIn, $checkOut) {
            $totalKamarFisik = Room::where('room_type_id', $type->id)
                ->where('status', '!=', 'maintenance')
                ->count();

            $bookedCount = Reservation::where('room_type_id', $type->id)
                ->whereNotIn('status', ['cancelled', 'checked_out'])
                ->where(function ($q) use ($checkIn, $checkOut) {
                    $q->where('check_in', '<', $checkOut)
                      ->where('check_out', '>', $checkIn);
                })
                ->sum('quantity');

            return [
                'id'              => $type->id,
                'name'            => $type->name,
                'price'           => $type->price,
                'description'     => $type->description,
                'facilities'      => $type->facilities,
                'image'           => $type->image,
                'total_rooms'     => $totalKamarFisik,
                'available_count' => max(0, $totalKamarFisik - $bookedCount),
            ];
        });

        return response()->json($availableRoomTypes);
    }

    // ─────────────────────────────────────────
    // GUEST: Create Booking
    // ─────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'id_tipe_kamar'     => 'required|exists:room_types,id',
            'tgl_check_in'      => 'required|date',
            'tgl_check_out'     => 'required|date|after:tgl_check_in',
            'jumlah_kamar'      => 'required|integer|min:1',
            'nama_pemesan'      => 'required|string|max:255',
            'email_pemesan'     => 'required|email|max:255',
            'no_hp'             => 'nullable|string|max:20',
            'metode_pembayaran' => 'required|string',
            'bukti_pembayaran'  => 'nullable|image|max:2048',
        ]);

        $roomType = RoomType::findOrFail($request->id_tipe_kamar);

        $totalKamarFisik = Room::where('room_type_id', $roomType->id)
            ->where('status', '!=', 'maintenance')
            ->count();

        $bookedCount = Reservation::where('room_type_id', $roomType->id)
            ->whereNotIn('status', ['cancelled', 'checked_out'])
            ->where(function ($q) use ($request) {
                $q->where('check_in', '<', $request->tgl_check_out)
                  ->where('check_out', '>', $request->tgl_check_in);
            })
            ->sum('quantity');

        $sisaStokAkurat = max(0, $totalKamarFisik - $bookedCount);

        if ($sisaStokAkurat < $request->jumlah_kamar) {
            return response()->json([
                'message' => 'Maaf, kuota unit fisik tipe ' . ($roomType->name) . ' penuh untuk range tanggal tersebut. Tersisa: ' . $sisaStokAkurat . ' unit.'
            ], 422);
        }

        $nights     = max(1, (strtotime($request->tgl_check_out) - strtotime($request->tgl_check_in)) / (60 * 60 * 24));
        $totalPrice = $roomType->price * $request->jumlah_kamar * $nights;

        $paymentMethod    = $request->metode_pembayaran === 'Transfer Bank' ? 'bank_transfer' : 'cash';
        $paymentProofPath = null;

        if ($request->hasFile('bukti_pembayaran')) {
            $paymentProofPath = $request->file('bukti_pembayaran')->store('payment_proofs', 'public');
        }

        $targetStatus = $request->has('status') ? $request->status : 'pending';

        DB::beginTransaction();
        try {
            $reservation = Reservation::create([
                'reservation_number' => 'RSV-' . strtoupper(Str::random(6)),
                'user_id'            => auth()->id(),
                'room_type_id'       => $roomType->id,
                'nama_tamu'          => $request->nama_pemesan,
                'email'              => $request->email_pemesan,
                'no_hp'              => $request->no_hp ?? '08123456789',
                'check_in'           => $request->tgl_check_in,
                'check_out'          => $request->tgl_check_out,
                'quantity'           => $request->jumlah_kamar,
                'total_price'        => $totalPrice,
                'status'             => $targetStatus,
                'payment_method'     => $paymentMethod,
                'payment_proof'      => $paymentProofPath,
            ]);

            DB::commit();

            $reservation->nomor_pemesanan = $reservation->reservation_number;
            $reservation->id_pemesanan    = $reservation->id;

            return response()->json([
                'message' => 'Pemesanan berhasil dibuat',
                'data'    => $reservation,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal membuat pesanan: ' . $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────
    // GUEST: My Reservations (PRIVASI KETAT)
    // ─────────────────────────────────────────
    public function myReservations()
    {
        if (!auth()->check()) {
            return response()->json(['success' => false, 'message' => 'Akses ditolak. Silakan login kembali.'], 401);
        }

        $data = Reservation::where('user_id', auth()->id())
            ->with(['roomType', 'rooms'])
            ->latest()
            ->get();

        $data->each(function ($r) {
            $r->id_pemesanan      = $r->id;
            $r->nomor_pemesanan   = $r->reservation_number;
            $r->nama_tipe         = $r->roomType?->name;
            $r->tgl_check_in      = $r->check_in;
            $r->tgl_check_out     = $r->check_out;
            $r->total_harga       = $r->total_price;
            $r->metode_pembayaran = $r->payment_method === 'bank_transfer' ? 'Transfer Bank' : 'Cash';
            $r->status_pemesanan  = match($r->status) {
                'pending'     => 'Baru',
                'verified'    => 'Verified',
                'checked_in'  => 'Check-in',
                'checked_out' => 'Check-out',
                'cancelled'   => 'Dibatalkan',
                default       => $r->status,
            };
        });

        return response()->json(['success' => true, 'data' => $data]);
    }

    // ─────────────────────────────────────────
    // GUEST: Upload Bukti Pembayaran
    // ─────────────────────────────────────────
    public function uploadProof(Request $request, $id)
    {
        $reservation = Reservation::findOrFail($id);
        $request->validate(['bukti_pembayaran' => 'required|image|max:2048']);

        $path = $request->file('bukti_pembayaran')->store('payment_proofs', 'public');
        
        $reservation->update([
            'payment_proof' => $path,
            'status' => 'verified'
        ]);

        return response()->json(['success' => true, 'message' => 'Bukti pembayaran berhasil diunggah']);
    }

    // ─────────────────────────────────────────
    // GUEST: Check Order Publik
    // ─────────────────────────────────────────
    public function checkOrder(Request $request)
    {
        $num   = $request->nomor_pemesanan ?? $request->reservation_number;
        $email = $request->email_pemesan  ?? $request->email;

        $reservation = Reservation::where('reservation_number', $num)
            ->where('email', $email)
            ->with(['roomType', 'rooms'])
            ->first();

        if (!$reservation) {
            return response()->json(['message' => 'Pesanan tidak ditemukan'], 404);
        }

        $reservation->status_pemesanan = match($reservation->status) {
            'pending'     => 'Baru',
            'verified'    => 'Verified',
            'checked_in'  => 'Check-in',
            'checked_out' => 'Check-out',
            'cancelled'   => 'Dibatalkan',
            default       => $reservation->status,
        };
        $reservation->total_harga   = $reservation->total_price;
        $reservation->nama_pemesan  = $reservation->nama_tamu;
        $reservation->email_pemesan = $reservation->email;
        $reservation->jumlah_kamar  = $reservation->quantity;
        $reservation->tgl_check_in  = $reservation->check_in;
        $reservation->tgl_check_out = $reservation->check_out;
        $reservation->nama_tipe     = $reservation->roomType?->name;

        return response()->json(['success' => true, 'data' => $reservation]);
    }

    // ─────────────────────────────────────────
    // RECEPTIONIST: List All Reservations
    // ─────────────────────────────────────────
    public function index(Request $request)
    {
        $query = Reservation::with(['user', 'roomType', 'rooms']);

        if ($request->check_in) {
            $query->where('check_in', $request->check_in);
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }

        $reservations = $query->latest()->get();
        $now = Carbon::now();

        $reservations->each(function ($res) use ($now) {
            $res->id_pemesanan      = $res->id;
            $res->nomor_pemesanan   = $res->reservation_number;
            $res->nama_pemesan      = $res->nama_tamu;
            $res->email_pemesan     = $res->email;
            $res->nama_tipe         = $res->roomType?->name;
            $res->tgl_check_in      = $res->check_in;
            $res->tgl_check_out     = $res->check_out;
            $res->total_harga       = $res->total_price;
            $res->metode_pembayaran = $res->payment_method === 'bank_transfer' ? 'Transfer Bank' : 'Cash';
            $res->proof_of_payment  = $res->payment_proof;
            $res->status_pemesanan  = match($res->status) {
                'pending'     => 'Baru',
                'verified'    => 'Verified',
                'checked_in'  => 'Check-in',
                'checked_out' => 'Check-out',
                'cancelled'   => 'Dibatalkan',
                default       => $res->status,
            };

            // Hitungan denda on-the-fly untuk list dashboard
            if ($res->status === 'checked_in') {
                $standardCheckOutTime = Carbon::parse($res->check_out . ' 12:00:00');
                if ($now->greaterThan($standardCheckOutTime)) {
                    $lateMinutes         = $now->diffInMinutes($standardCheckOutTime);
                    $lateHours           = (int) ceil($lateMinutes / 60);
                    $res->potential_fine = $lateHours * 50000;
                    $res->late_minutes   = $lateMinutes;
                    $res->jam_terlambat  = $lateHours;
                }
            }
        });

        return response()->json(['success' => true, 'data' => $reservations]);
    }

    // ─────────────────────────────────────────
    // RECEPTIONIST: Update Status
    // ─────────────────────────────────────────
    public function updateStatus(Request $request, $id)
    {
        $allowedStatuses = ['verified', 'checked_in', 'checked_out', 'cancelled'];
        $targetStatus    = in_array($request->status, $allowedStatuses) ? $request->status : null;

        if (!$targetStatus) {
            return response()->json(['message' => 'Status tidak valid.'], 422);
        }

        $reservation = Reservation::findOrFail($id);
        $updateData  = ['status' => $targetStatus];

        if ($request->has('fine')) {
            $updateData['late_fine'] = $request->fine;
        }

        DB::beginTransaction();
        try {
            $reservation->update($updateData);

            if ($targetStatus === 'checked_in' && $request->has('room_ids')) {
                $reservation->rooms()->sync($request->room_ids);
                Room::whereIn('id', $request->room_ids)->update(['status' => 'occupied']);
            }

            if ($targetStatus === 'checked_out') {
                $roomIds = $reservation->rooms()->pluck('rooms.id');
                Room::whereIn('id', $roomIds)->update(['status' => 'dirty']);
            }

            if ($targetStatus === 'cancelled') {
                $roomIds = $reservation->rooms()->pluck('rooms.id');
                Room::whereIn('id', $roomIds)->update(['status' => 'available']);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Status pesanan berhasil diubah']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal: ' . $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────
    // RECEPTIONIST: Checkout Info + Hitung Denda Akurat
    // ─────────────────────────────────────────
    public function checkoutInfo($id)
    {
        $reservation          = Reservation::with('roomType')->findOrFail($id);
        
        // BATAS CHECKOUT HOTEL ADALAH JAM 12:00 SIANG
        $standardCheckOutTime = Carbon::parse($reservation->check_out . ' 12:00:00');
        $now                  = Carbon::now();

        $lateMinutes = 0;
        $lateHours   = 0;
        $fine        = 0;

        // ✅ FIX: LOGIKA MATEMATIKA DENDA RP 50.000 / JAM OVERTIME
        if ($now->greaterThan($standardCheckOutTime)) {
            $lateMinutes = $now->diffInMinutes($standardCheckOutTime);
            // Pembulatan ke atas. Lewat 1 menit = dihitung 1 jam.
            $lateHours   = (int) ceil($lateMinutes / 60);
            $fine        = $lateHours * 50000;
        }

        return response()->json([
            'room_name'         => $reservation->roomType->name ?? 'Kamar',
            'standard_checkout' => $standardCheckOutTime->format('Y-m-d H:i:s'),
            'actual_checkout'   => $now->format('Y-m-d H:i:s'),
            'late_minutes'      => $lateMinutes,
            'jam_terlambat'     => $lateHours,
            'fine'              => $fine,
        ]);
    }

    // ─────────────────────────────────────────
    // RECEPTIONIST + ADMIN: Daily Report
    // ─────────────────────────────────────────
    public function dailyReport(Request $request)
    {
        $date = $request->date ?? date('Y-m-d');

        $checkIn  = Reservation::with(['user', 'roomType'])
            ->where('check_in', $date)
            ->where('status', '!=', 'cancelled')
            ->get();

        $checkOut = Reservation::with(['user', 'roomType'])
            ->where('check_out', $date)
            ->where('status', '!=', 'cancelled')
            ->get();

        foreach ([$checkIn, $checkOut] as $list) {
            $list->each(function ($r) {
                $r->nomor_pemesanan = $r->reservation_number;
                $r->nama_pemesan    = $r->nama_tamu;
                $r->nama_tipe       = $r->roomType?->name;
                $r->status_label    = match($r->status) {
                    'checked_in'  => 'Check-in',
                    'checked_out' => 'Check-out',
                    default       => 'Verified',
                };
            });
        }

        return response()->json([
            'date'      => $date,
            'check_in'  => $checkIn,
            'check_out' => $checkOut,
        ]);
    }

    // ─────────────────────────────────────────
    // ADMIN: Revenue Report
    // ─────────────────────────────────────────
    public function report(Request $request)
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        $reservations = Reservation::with(['user', 'roomType'])
            ->whereIn('status', ['verified', 'checked_in', 'checked_out'])
            ->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date   . ' 23:59:59',
            ])
            ->get();

        $reservations->each(function ($r) {
            $r->id_pemesanan    = $r->id;
            $r->nomor_pemesanan = $r->reservation_number;
            $r->nama_pemesan    = $r->nama_tamu;
            $r->nama_tipe       = $r->roomType?->name;
            $r->status_label    = $r->status === 'checked_out' ? 'Check-out' : 'Lunas';
        });

        return response()->json([
            'total_revenue' => $reservations->sum('total_price'),
            'reservations'  => $reservations,
        ]);
    }

    // ─────────────────────────────────────────
    // RECEPTIONIST: Get Available Rooms for Check-in
    // ─────────────────────────────────────────
    public function getAvailableRoomsForBooking(Request $request)
    {
        $rooms = Room::where('room_type_id', $request->room_type_id)
            ->where('status', '!=', 'maintenance')
            ->get();

        return response()->json($rooms->values());
    }
}