<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class RoomTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $checkIn = $request->query('check_in') ?: date('Y-m-d');
        $checkOut = $request->query('check_out') ?: date('Y-m-d', strtotime('+1 day'));

        $roomTypes = RoomType::all();

        foreach ($roomTypes as $tipe) {
            try {
                $totalFisik = Room::where('room_type_id', $tipe->id)
                    ->where('status', '!=', 'maintenance')
                    ->count();

                $kamarTerpakai = DB::table('reservation_room')
                    ->join('reservations', 'reservation_room.reservation_id', '=', 'reservations.id')
                    ->where('reservations.room_type_id', $tipe->id)
                    ->where('reservations.status', '!=', 'cancelled')
                    ->where(function ($query) use ($checkIn, $checkOut) {
                        $query->where('reservations.check_in', '<', $checkOut)
                              ->where('reservations.check_out', '>', $checkIn);
                    })
                    ->count();

                $hitungAkurat = max(0, $totalFisik - $kamarTerpakai);
                $tipe->available_rooms_count = $hitungAkurat;
                $tipe->available_count = $hitungAkurat; 

                $tipe->nama_tipe = $tipe->name; 
                $tipe->harga = $tipe->price;
                $tipe->jumlah_kamar = $tipe->stock;
                $tipe->deskripsi = $tipe->description;
                $tipe->fasilitas = $tipe->facilities;
                $tipe->foto = $tipe->image;
            } catch (\Exception $e) {
                $tipe->available_rooms_count = $tipe->stock;
                $tipe->available_count = $tipe->stock;
                $tipe->nama_tipe = $tipe->name;
                $tipe->harga = $tipe->price;
                $tipe->jumlah_kamar = $tipe->stock;
                $tipe->deskripsi = $tipe->description;
                $tipe->fasilitas = $tipe->facilities;
                $tipe->foto = $tipe->image;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $roomTypes
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $roomType = RoomType::find($id);
        if (!$roomType) {
            return response()->json(['success' => false, 'message' => 'Tipe kamar tidak ditemukan'], 404);
        }
        
        $roomType->nama_tipe = $roomType->name;
        $roomType->harga = $roomType->price;
        $roomType->jumlah_kamar = $roomType->stock;
        $roomType->deskripsi = $roomType->description;
        $roomType->fasilitas = $roomType->facilities;
        $roomType->foto = $roomType->image;

        return response()->json([
            'success' => true,
            'data' => $roomType
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ✅ FIX PROTEKSI DUPLIKAT: Mengunci nama_tipe agar harus unique di tabel room_types
        $request->validate([
            'nama_tipe' => 'required|string|unique:room_types,name',
            'harga' => 'required|numeric',
            'jumlah_kamar' => 'required|numeric',
            'deskripsi' => 'required|string',
            'fasilitas' => 'required|string',
            'foto' => 'nullable|image|max:2048'
        ], [
            'nama_tipe.unique' => 'Nama tipe kamar ini sudah digunakan! Mohon buat nama yang berbeda.'
        ]);

        $roomType = new RoomType();
        $roomType->name = $request->nama_tipe;
        $roomType->price = (int) $request->harga;
        $roomType->stock = (int) $request->jumlah_kamar;
        $roomType->description = $request->deskripsi;
        $roomType->facilities = $request->fasilitas;

        if ($request->hasFile('foto')) {
            $roomType->image = $request->file('foto')->store('room_types', 'public');
        }

        $roomType->save();

        $roomType->nama_tipe = $roomType->name;
        $roomType->harga = $roomType->price;
        $roomType->jumlah_kamar = $roomType->stock;

        return response()->json([
            'success' => true,
            'message' => 'Tipe Kamar berhasil ditambah!',
            'data' => $roomType
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $roomType = RoomType::find($id);
        if (!$roomType) {
            return response()->json(['success' => false, 'message' => 'Tipe kamar tidak ditemukan'], 404);
        }

        // ✅ FIX PROTEKSI DUPLIKAT PAS UPDATE: Abaikan pengecekan jika namanya sama dengan miliknya sendiri
        $request->validate([
            'nama_tipe' => 'required|string|unique:room_types,name,' . $id,
            'harga' => 'required|numeric',
            'jumlah_kamar' => 'required|numeric',
            'deskripsi' => 'required|string',
            'fasilitas' => 'required|string',
        ], [
            'nama_tipe.unique' => 'Nama tipe kamar ini sudah digunakan oleh tipe lain! Mohon cari nama lain.'
        ]);

        $roomType->name = $request->nama_tipe;
        $roomType->price = (int) $request->harga;
        $roomType->stock = (int) $request->jumlah_kamar;
        $roomType->description = $request->deskripsi;
        $roomType->facilities = $request->fasilitas;

        if ($request->hasFile('foto')) {
            if ($roomType->image) {
                Storage::disk('public')->delete($roomType->image);
            }
            $roomType->image = $request->file('foto')->store('room_types', 'public');
        }

        $roomType->save();

        $roomType->nama_tipe = $roomType->name;
        $roomType->harga = $roomType->price;
        $roomType->jumlah_kamar = $roomType->stock;

        return response()->json([
            'success' => true,
            'message' => 'Tipe Kamar berhasil diupdate!',
            'data' => $roomType
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $roomType = RoomType::find($id);
        if (!$roomType) {
            return response()->json(['success' => false, 'message' => 'Tipe kamar tidak ditemukan'], 404);
        }
        
        if ($roomType->image) {
            Storage::disk('public')->delete($roomType->image);
        }

        $roomType->delete();
        return response()->json([
            'success' => true,
            'message' => 'Tipe Kamar berhasil dihapus!'
        ]);
    }
}