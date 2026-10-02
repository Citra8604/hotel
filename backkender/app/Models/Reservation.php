<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_number',
        'user_id',
        'room_type_id',
        'nama_tamu',
        'email',
        'no_hp',
        'check_in',
        'check_out',
        'quantity',
        'total_price',
        'late_fine',
        'payment_method',
        'payment_proof',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class);
    }
}
