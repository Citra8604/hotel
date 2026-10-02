<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'description',
        'facilities',
        'image',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
