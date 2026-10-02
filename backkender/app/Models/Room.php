<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'room_number',
        'room_type_id',
        'status',
    ];

    // ✅ FIX: Mempertegas foreign key agar query JOIN tidak tersesat
    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id', 'id');
    }

    // ✅ FIX: Fungsi alias bahasa Indonesia biar selalu match dengan tarikan frontend lu
    public function tipe_kamar()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id', 'id');
    }

    public function reservations()
    {
        return $this->belongsToMany(Reservation::class);
    }
}