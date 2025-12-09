<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amenity extends Model
{
    use HasFactory;

    protected $table = 'amenities';
    protected $primaryKey = 'amenities_id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'amenities_id',
        'amenities_name',
        'user_id'
    ];

    /**
     * Relationship with owner
     */
    public function owner()
    {
        return $this->belongsTo(Account::class, 'user_id', 'user_id');
    }

    /**
     * Relationship with accommodations
     */
    public function accommodations()
    {
        return $this->belongsToMany(Accommodation::class, 'amenities_selected', 'amenities_id', 'accomodation_id')
            ->withTimestamps()
            ->withPivot('selected_id');
    }

    /**
     * Scope for amenities by owner
     */
    public function scopeByOwner($query, $ownerId)
    {
        return $query->where('user_id', $ownerId);
    }

    /**
     * Scope for common amenities (system-wide)
     */
    public function scopeCommon($query)
    {
        $commonAmenities = ['Wifi', 'Airconditioner', 'TV', 'Comfort Room', 'Kitchen', 'Laundry', 'Parking'];
        return $query->whereIn('amenities_name', $commonAmenities);
    }

    /**
     * Scope for amenities by name
     */
    public function scopeByName($query, $name)
    {
        return $query->where('amenities_name', 'like', '%' . $name . '%');
    }

    /**
     * Get icon based on amenity name
     */
    public function getIconAttribute(): string
    {
        $name = strtolower($this->amenities_name);
        
        $iconMap = [
            'wifi' => 'wifi',
            'airconditioner' => 'snowflake',
            'air conditioner' => 'snowflake',
            'ac' => 'snowflake',
            'tv' => 'tv',
            'television' => 'tv',
            'comfort room' => 'bath',
            'cr' => 'bath',
            'toilet' => 'bath',
            'bathroom' => 'bath',
            'kitchen' => 'utensils',
            'laundry' => 'tshirt',
            'washing' => 'tshirt',
            'parking' => 'car',
            'garage' => 'car',
            'security' => 'shield-alt',
            'cctv' => 'video',
            'water' => 'tint',
            'electricity' => 'bolt',
            'power' => 'bolt',
            'bed' => 'bed',
            'foam' => 'bed',
            'mattress' => 'bed',
            'fan' => 'fan',
            'refrigerator' => 'snowflake',
            'fridge' => 'snowflake',
            'microwave' => 'fire',
            'heater' => 'fire',
            'shower' => 'shower',
            'hot water' => 'fire',
        ];
        
        foreach ($iconMap as $keyword => $icon) {
            if (str_contains($name, $keyword)) {
                return $icon;
            }
        }
        
        return 'check-circle';
    }

    /**
     * Get icon color
     */
    public function getIconColorAttribute(): string
    {
        $colors = [
            'wifi' => 'primary',
            'snowflake' => 'info',
            'tv' => 'success',
            'bath' => 'warning',
            'utensils' => 'danger',
            'tshirt' => 'secondary',
            'car' => 'dark',
            'shield-alt' => 'primary',
            'tint' => 'info',
            'bolt' => 'warning',
            'bed' => 'success',
            'fan' => 'secondary',
            'fire' => 'danger',
            'shower' => 'info',
            'video' => 'dark',
        ];
        
        return $colors[$this->icon] ?? 'secondary';
    }

    /**
     * Get formatted amenity name
     */
    public function getFormattedNameAttribute(): string
    {
        return ucwords(strtolower($this->amenities_name));
    }

    /**
     * Get amenity category
     */
    public function getCategoryAttribute(): string
    {
        $name = strtolower($this->amenities_name);
        
        if (str_contains($name, 'wifi') || str_contains($name, 'tv')) {
            return 'Entertainment';
        } elseif (str_contains($name, 'air') || str_contains($name, 'fan') || str_contains($name, 'heater')) {
            return 'Climate Control';
        } elseif (str_contains($name, 'bath') || str_contains($name, 'toilet') || str_contains($name, 'shower')) {
            return 'Bathroom';
        } elseif (str_contains($name, 'kitchen') || str_contains($name, 'refrigerator') || str_contains($name, 'microwave')) {
            return 'Kitchen';
        } elseif (str_contains($name, 'laundry') || str_contains($name, 'washing')) {
            return 'Laundry';
        } elseif (str_contains($name, 'parking') || str_contains($name, 'garage')) {
            return 'Parking';
        } elseif (str_contains($name, 'security') || str_contains($name, 'cctv')) {
            return 'Security';
        } elseif (str_contains($name, 'bed') || str_contains($name, 'mattress') || str_contains($name, 'foam')) {
            return 'Furniture';
        } else {
            return 'General';
        }
    }

    /**
     * Get usage count (how many accommodations have this amenity)
     */
    public function getUsageCountAttribute(): int
    {
        return $this->accommodations()->count();
    }

    /**
     * Check if amenity is common
     */
    public function isCommon(): bool
    {
        $commonAmenities = ['wifi', 'airconditioner', 'air conditioner', 'tv', 'television', 'comfort room', 'cr', 'toilet', 'bathroom'];
        $name = strtolower($this->amenities_name);
        
        foreach ($commonAmenities as $common) {
            if (str_contains($name, $common)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Get similar amenities
     */
    public function getSimilarAmenitiesAttribute($limit = 5)
    {
        $category = $this->category;
        
        return self::where('amenities_id', '!=', $this->amenities_id)
            ->where(function($query) use ($category) {
                $query->whereRaw('LOWER(amenities_name) LIKE ?', ['%' . strtolower($category) . '%'])
                      ->orWhere('category', $category);
            })
            ->limit($limit)
            ->get();
    }

    /**
     * Create system default amenities
     */
    public static function createDefaultAmenities($ownerId = null): array
    {
        $defaultAmenities = [
            ['name' => 'Wifi', 'icon' => 'wifi'],
            ['name' => 'Airconditioner', 'icon' => 'snowflake'],
            ['name' => 'TV', 'icon' => 'tv'],
            ['name' => 'Comfort Room', 'icon' => 'bath'],
            ['name' => 'Private CR', 'icon' => 'bath'],
            ['name' => 'Kitchen', 'icon' => 'utensils'],
            ['name' => 'Laundry Area', 'icon' => 'tshirt'],
            ['name' => 'Parking Space', 'icon' => 'car'],
            ['name' => '24/7 Security', 'icon' => 'shield-alt'],
            ['name' => 'CCTV', 'icon' => 'video'],
            ['name' => 'Water Heater', 'icon' => 'fire'],
            ['name' => 'Electric Fan', 'icon' => 'fan'],
            ['name' => 'Foam Bed', 'icon' => 'bed'],
            ['name' => 'Study Table', 'icon' => 'desk'],
            ['name' => 'Cabinet', 'icon' => 'archive'],
        ];

        $createdAmenities = [];
        
        foreach ($defaultAmenities as $amenity) {
            $amenityId = 'amenity_' . strtolower(str_replace(' ', '_', $amenity['name'])) . '_' . uniqid();
            
            $created = self::create([
                'amenities_id' => $amenityId,
                'amenities_name' => $amenity['name'],
                'user_id' => $ownerId
            ]);
            
            if ($created) {
                $createdAmenities[] = $created;
            }
        }

        return $createdAmenities;
    }

    /**
     * Get top amenities by usage
     */
    public static function getTopAmenities($limit = 10)
    {
        return self::withCount('accommodations')
            ->orderBy('accommodations_count', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($amenity) {
                return [
                    'id' => $amenity->amenities_id,
                    'name' => $amenity->formatted_name,
                    'icon' => $amenity->icon,
                    'usage_count' => $amenity->accommodations_count,
                    'category' => $amenity->category,
                    'is_common' => $amenity->isCommon(),
                ];
            });
    }
}