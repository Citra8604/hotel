<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoomType;
use App\Models\Room;

class RoomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = Room::with(['roomType', 'tipe_kamar']);

            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            if ($request->has('room_type_id')) {
                $query->where('room_type_id', $request->room_type_id);
            }

            $rooms = $query->get();

            foreach ($rooms as $room) {
                $room->nomor_kamar = $room->room_number ?? $room->nomor_kamar;
                $room->id_tipe_kamar = $room->room_type_id ?? $room->id_tipe_kamar;
                
                $relasiTipe = $room->roomType ?? $room->tipe_kamar;
                if ($relasiTipe) {
                    $room->tipe_kamar = [
                        'nama_tipe' => $relasiTipe->name ?? $relasiTipe->nama_tipe ?? 'Standard'
                    ];
                } else {
                    $room->tipe_kamar = ['nama_tipe' => 'Standard'];
                }

                if ($room->status === 'available') $room->status = 'Tersedia';
                if ($room->status === 'occupied') $room->status = 'Terisi';
                if ($room->status === 'dirty') $room->status = 'Kotor';
                if ($room->status === 'maintenance') $room->status = 'Perbaikan';
            }

            return response()->json([
                'success' => true,
                'data' => $rooms
            ]);

        } catch (\Exception $e) {
            $fallbackRooms = Room::all();
            foreach ($fallbackRooms as $room) {
                $room->nomor_kamar = $room->room_number ?? $room->nomor_kamar ?? '101';
                $room->tipe_kamar = ['nama_tipe' => 'Standard'];
                if ($room->status === 'available') $room->status = 'Tersedia';
            }
            return response()->json([
                'success' => true,
                'data' => $fallbackRooms
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_number' => 'required|string',
            'room_type_id' => 'required',
            'status' => 'required',
        ]);

        // ✅ FIX PROTEKSI DUPLIKAT: Cek apakah nomor unit kamar fisik sudah terdaftar di database
        $isDuplikat = Room::where('room_number', $request->room_number)->exists();
        if ($isDuplikat) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal tambah unit! Maaf, nomor kamar ' . $request->room_number . ' sudah digunakan di sistem.'
            ], 422);
        }

        $roomType = RoomType::find($request->room_type_id);
        $currentRoomCount = Room::where('room_type_id', $request->room_type_id)->count();
        $maxStock = $roomType ? ($roomType->stock ?? $roomType->jumlah_kamar ?? 0) : 0;

        if ($roomType && $currentRoomCount >= $maxStock) {
            return response()->json([
                'success' => false,
                'message' => "Gagal tambah kamar! Batas maksimal kuota tipe " . ($roomType->name) . " sudah penuh."
            ], 422);
        }

        $statusDb = 'available';
        if ($request->status === 'Tersedia') $statusDb = 'available';
        elseif ($request->status === 'Terisi') $statusDb = 'occupied';
        elseif ($request->status === 'Kotor') $statusDb = 'dirty';
        elseif ($request->status === 'Perbaikan') $statusDb = 'maintenance';
        else $statusDb = $request->status;

        $room = new Room();
        $room->room_number = $request->room_number;
        $room->room_type_id = $request->room_type_id;
        $room->status = $statusDb;
        $room->save();

        return response()->json([
            'success' => true,
            'message' => 'Unit kamar baru berhasil didaftarkan!',
            'data' => $room
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $room = Room::find($id);
        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Kamar tidak ditemukan'], 404);
        }

        $request->validate([
            'room_number' => 'required|string',
            'room_type_id' => 'required',
            'status' => 'required',
        ]);

        // ✅ FIX PROTEKSI DUPLIKAT PAS EDIT: Abaikan ID kamar yang sedang diedit saat ini
        $isDuplikat = Room::where('room_number', $request->room_number)->where('id', '!=', $id)->exists();
        if ($isDuplikat) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui! Nomor kamar ' . $request->room_number . ' sudah dialokasikan ke unit lain.'
            ], 422);
        }

        $statusDb = 'available';
        if ($request->status === 'Tersedia') $statusDb = 'available';
        elseif ($request->status === 'Terisi') $statusDb = 'occupied';
        elseif ($request->status === 'Kotor') $statusDb = 'dirty';
        elseif ($request->status === 'Perbaikan') $statusDb = 'maintenance';
        else $statusDb = $request->status;

        $room->room_number = $request->room_number;
        $room->room_type_id = $request->room_type_id;
        $room->status = $statusDb;
        $room->save();

        return response()->json([
            'success' => true,
            'message' => 'Data kamar berhasil diperbarui!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $room = Room::find($id);
        if (!$room) {
            return response()->json(['success' => false, 'message' => 'Kamar fisik tidak ditemukan'], 404);
        }
        
        $room->delete();
        return response()->json([
            'success' => true,
            'message' => 'Kamar berhasil dihapus!'
        ]);
    }
}