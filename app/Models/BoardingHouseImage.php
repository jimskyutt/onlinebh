<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardingHouseImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'boarding_house_id',
        'image_path',
        'is_primary',
        'display_order'
    ];

    protected $casts = [
        'is_primary' => 'boolean'
    ];

    public function boardingHouse()
    {
        return $this->belongsTo(BoardingHouse::class);
    }
}
