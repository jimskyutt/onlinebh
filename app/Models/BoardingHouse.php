<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardingHouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'space_type',
        'status',
        'description',
        'price',
        'bed_space_price',
        'bed_space_per_room',
        'room_space_price',
        'available_rooms',
        'max_rooms',
        'max_boarders',
        'current_boarders',
        'contact_person',
        'contact_number',
        'address',
        'latitude',
        'longitude',
        'amenities'
    ];

    protected $casts = [
        'bed_space_price' => 'decimal:2',
        'bed_space_per_room' => 'integer',
        'room_space_price' => 'decimal:2',
        'price' => 'decimal:2'
    ];

    protected $with = ['images'];

    /**
     * Get the owner of the boarding house.
     */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    /**
     * The facilities that belong to the boarding house.
     */
    public function facilities()
    {
        return $this->belongsToMany(Facility::class, 'boarding_house_facilities', 'boarding_house_id', 'facility_id')
            ->withTimestamps();
    }

    public function images()
    {
        return $this->hasMany(BoardingHouseImage::class)->orderBy('display_order');
    }
}