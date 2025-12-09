<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfileInfo extends Model
{
    use HasFactory;

    protected $table = 'profile_info';
    protected $primaryKey = 'user_id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'image_profile',
        'bio'
    ];

    /**
     * Relationship with user
     */
    public function user()
    {
        return $this->belongsTo(Account::class, 'user_id', 'user_id');
    }

    /**
     * Get profile image URL
     */
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image_profile) {
            return null;
        }
        
        if (filter_var($this->image_profile, FILTER_VALIDATE_URL)) {
            return $this->image_profile;
        }
        
        return asset('storage/profiles/' . $this->image_profile);
    }
}