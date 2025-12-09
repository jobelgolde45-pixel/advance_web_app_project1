<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Accommodation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'accomodations';
    protected $primaryKey = 'accomodation_id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'accomodation_id',
        'accomodation_type',
        'room_name',
        'monthly_rate',
        'max_occupants',
        'gender_preference',
        'status',
        'total_beds',
        'user_id',
        'description',
        'date_created',
        'date_updated',
        'location'
    ];

    protected $casts = [
        'monthly_rate' => 'float',
        'max_occupants' => 'integer',
        'total_beds' => 'integer',
        'date_created' => 'date',
        'date_updated' => 'date',
    ];

    /**
     * Relationship with owner
     */
    public function owner()
    {
        return $this->belongsTo(Account::class, 'user_id', 'user_id')
            ->where('user_type', 'Owner');
    }

    /**
     * Relationship with amenities (many-to-many through amenities_selected)
     */
    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'amenities_selected', 'accomodation_id', 'amenities_id')
            ->withTimestamps()
            ->withPivot('selected_id');
    }

    /**
     * Relationship with room images
     */
    public function images()
    {
        return $this->hasMany(RoomImage::class, 'accomodation_id', 'accomodation_id');
    }

    /**
     * Relationship with deck images
     */
    public function deckImages()
    {
        return $this->hasMany(DeckImage::class, 'accomodation_id', 'accomodation_id');
    }

    /**
     * Relationship with reservations
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'accomodation_id', 'accomodation_id');
    }

    /**
     * Relationship with current occupants
     */
    public function occupants()
    {
        return $this->hasMany(Occupant::class, 'accomodation_id', 'accomodation_id')
            ->where('status', 'On Going');
    }

    /**
     * Relationship with accommodation lists
     */
    public function accommodationLists()
    {
        return $this->hasMany(AccommodationList::class, 'accomodation_id', 'accomodation_id');
    }

    /**
     * Scope for available accommodations
     */
    public function scopeAvailable($query)
    {
        return $query->where('status', 'Available');
    }

    /**
     * Scope for accommodations by type
     */
    public function scopeType($query, $type)
    {
        return $query->where('accomodation_type', $type);
    }

    /**
     * Scope for accommodations by gender preference
     */
    public function scopeGender($query, $gender)
    {
        return $query->where('gender_preference', $gender);
    }

    /**
     * Scope for accommodations in Bulan, Sorsogon
     */
    public function scopeInBulan($query)
    {
        return $query->where('location', 'like', '%Bulan%')
            ->orWhereHas('owner', function($q) {
                $q->where('municipality', 'Bulan')
                  ->where('province', 'Sorsogon');
            });
    }

    /**
     * Scope for accommodations by price range
     */
    public function scopePriceRange($query, $min, $max)
    {
        return $query->whereBetween('monthly_rate', [$min, $max]);
    }

    /**
     * Scope for accommodations with specific amenities
     */
    public function scopeWithAmenities($query, array $amenityIds)
    {
        return $query->whereHas('amenities', function($q) use ($amenityIds) {
            $q->whereIn('amenities_id', $amenityIds);
        }, '=', count($amenityIds));
    }

    /**
     * Get available beds count
     */
    public function getAvailableBedsAttribute(): int
    {
        $occupiedBeds = Reservation::where('accomodation_id', $this->accomodation_id)
            ->where('reservation_status', 'Approved')
            ->whereDate('end_date', '>=', now())
            ->count();

        return max(0, $this->total_beds - $occupiedBeds);
    }

    /**
     * Get occupancy percentage
     */
    public function getOccupancyPercentageAttribute(): float
    {
        if ($this->total_beds == 0) return 0;
        
        $occupiedBeds = Reservation::where('accomodation_id', $this->accomodation_id)
            ->where('reservation_status', 'Approved')
            ->whereDate('end_date', '>=', now())
            ->count();

        return ($occupiedBeds / $this->total_beds) * 100;
    }

    /**
     * Check if accommodation is fully occupied
     */
    public function isFullyOccupied(): bool
    {
        return $this->available_beds === 0;
    }

    /**
     * Get featured image
     */
    public function getFeaturedImageAttribute()
    {
        return $this->images->first();
    }

    /**
     * Get daily rate (approximate)
     */
    public function getDailyRateAttribute(): float
    {
        return round($this->monthly_rate / 30, 2);
    }

    /**
     * Get formatted monthly rate
     */
    public function getFormattedMonthlyRateAttribute(): string
    {
        return '₱' . number_format($this->monthly_rate, 2);
    }

    /**
     * Get accommodation type with icon
     */
    public function getTypeWithIconAttribute(): array
    {
        $icons = [
            'Boarding House' => '🏠',
            'Lodging House' => '🛏️',
            'Dormitory' => '🏢',
            'Apartment' => '🏘️'
        ];

        return [
            'type' => $this->accomodation_type,
            'icon' => $icons[$this->accomodation_type] ?? '🏠'
        ];
    }

    /**
     * Update accommodation status based on occupancy
     */
    public function updateStatus(): void
    {
        $occupiedBeds = Reservation::where('accomodation_id', $this->accomodation_id)
            ->where('reservation_status', 'Approved')
            ->whereDate('end_date', '>=', now())
            ->count();

        if ($occupiedBeds == 0) {
            $this->status = 'Available';
        } elseif ($occupiedBeds == $this->total_beds) {
            $this->status = 'Occupied';
        } else {
            $this->status = 'Partially Occupied';
        }

        $this->save();
    }

    /**
     * Get similar accommodations (same owner or nearby)
     */
    public function similarAccommodations($limit = 3)
    {
        return self::where('user_id', $this->user_id)
            ->where('accomodation_id', '!=', $this->accomodation_id)
            ->where('status', 'Available')
            ->limit($limit)
            ->get();
    }

    /**
     * Check if accommodation is suitable for gender
     */
    public function isSuitableForGender($gender): bool
    {
        if ($this->gender_preference === 'Unisex') {
            return true;
        }

        if ($gender === 'Male' && $this->gender_preference === 'Male') {
            return true;
        }

        if ($gender === 'Female' && $this->gender_preference === 'Female') {
            return true;
        }

        return false;
    }

    /**
     * Get accommodation rating (if implemented later)
     */
    public function getRatingAttribute(): array
    {
        // This would connect to a ratings table if implemented
        return [
            'average' => 4.5, // Placeholder
            'count' => 12,    // Placeholder
            'reviews' => []   // Placeholder
        ];
    }

    /**
     * Get verification status based on owner DTI permit
     */
    public function getVerificationStatusAttribute(): string
    {
        if ($this->owner && $this->owner->dti_permit) {
            return 'Verified';
        }
        
        return 'Unverified';
    }
}