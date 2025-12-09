<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomImage extends Model
{
    use HasFactory;

    protected $table = 'room_images';
    protected $primaryKey = 'room_images_id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'room_images_id',
        'accomodation_id',
        'image_path'
    ];

    /**
     * Relationship with accommodation
     */
    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class, 'accomodation_id', 'accomodation_id');
    }

    /**
     * Get full image URL
     */
    public function getImageUrlAttribute(): string
    {
        if (filter_var($this->image_path, FILTER_VALIDATE_URL)) {
            return $this->image_path;
        }
        
        return asset('storage/accommodations/' . $this->image_path);
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute(): string
    {
        $path = $this->image_path;
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        $thumbnail = $filename . '_thumb.' . $extension;
        
        return asset('storage/accommodations/thumbnails/' . $thumbnail);
    }
}