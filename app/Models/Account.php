<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Account extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'accounts';
    protected $primaryKey = 'user_id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id',
        'firstname',
        'middlename',
        'lastname',
        'extension_name',
        'province',
        'municipality',
        'barangay',
        'zipcode',
        'email',
        'mobile_number',
        'dti_permit',
        'location',
        'profile',
        'house_name',
        'date_registered',
        'user_type',
        'username',
        'password',
        'status',
        'view',
        'remember_token'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'date_registered' => 'date',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Authentication identifier
    public function getAuthIdentifierName()
    {
        return 'user_id';
    }

    /**
     * Relationship with accommodations (if user is owner)
     */
    public function accommodations()
    {
        return $this->hasMany(Accommodation::class, 'user_id', 'user_id');
    }

    /**
     * Relationship with sent notifications
     */
    public function sentNotifications()
    {
        return $this->hasMany(Notification::class, 'sender', 'user_id');
    }

    /**
     * Relationship with received notifications
     */
    public function receivedNotifications()
    {
        return $this->hasMany(Notification::class, 'receiver', 'user_id');
    }

    /**
     * Relationship with reservations as tenant
     */
    public function tenantReservations()
    {
        return $this->hasMany(Reservation::class, 'tenant_user_id', 'user_id');
    }

    /**
     * Relationship with reservations as owner
     */
    public function ownerReservations()
    {
        return $this->hasMany(Reservation::class, 'owner_user_id', 'user_id');
    }

    /**
     * Relationship with occupants
     */
    public function occupants()
    {
        return $this->hasMany(Occupant::class, 'tenant_id', 'user_id');
    }

    /**
     * Relationship with accommodation lists
     */
    public function accommodationLists()
    {
        return $this->hasMany(AccommodationList::class, 'user_id', 'user_id');
    }

    /**
     * Relationship with profile info
     */
    public function profileInfo()
    {
        return $this->hasOne(ProfileInfo::class, 'user_id', 'user_id');
    }

    /**
     * Relationship with amenities (for owners)
     */
    public function amenities()
    {
        return $this->hasMany(Amenity::class, 'user_id', 'user_id');
    }

    /**
     * Scope for owners only
     */
    public function scopeOwners($query)
    {
        return $query->where('user_type', 'Owner');
    }

    /**
     * Scope for tenants only
     */
    public function scopeTenants($query)
    {
        return $query->where('user_type', 'Tenant');
    }

    /**
     * Scope for admins only
     */
    public function scopeAdmins($query)
    {
        return $query->where('user_type', 'Admin');
    }

    /**
     * Scope for approved users only
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    /**
     * Scope for pending users
     */
    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    /**
     * Scope for users in Bulan, Sorsogon
     */
    public function scopeInBulan($query)
    {
        return $query->where('municipality', 'Bulan')
                     ->where('province', 'Sorsogon');
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        $name = $this->firstname;
        
        if (!empty($this->middlename)) {
            $name .= ' ' . substr($this->middlename, 0, 1) . '.';
        }
        
        $name .= ' ' . $this->lastname;
        
        if (!empty($this->extension_name)) {
            $name .= ' ' . $this->extension_name;
        }
        
        return $name;
    }

    /**
     * Get short name (first name + last name initial)
     */
    public function getShortNameAttribute(): string
    {
        return $this->firstname . ' ' . substr($this->lastname, 0, 1) . '.';
    }

    /**
     * Get complete address
     */
    public function getCompleteAddressAttribute(): string
    {
        $addressParts = [];
        
        if (!empty($this->barangay)) {
            $addressParts[] = $this->barangay;
        }
        
        if (!empty($this->municipality)) {
            $addressParts[] = $this->municipality;
        }
        
        if (!empty($this->province)) {
            $addressParts[] = $this->province;
        }
        
        if (!empty($this->zipcode)) {
            $addressParts[] = $this->zipcode;
        }
        
        return implode(', ', $addressParts);
    }

    /**
     * Get profile photo URL
     */
    public function getProfilePhotoUrlAttribute(): ?string
    {
        if (!$this->profile) {
            return null;
        }
        
        if (filter_var($this->profile, FILTER_VALIDATE_URL)) {
            return $this->profile;
        }
        
        return asset('storage/profiles/' . $this->profile);
    }

    /**
     * Get DTI permit URL
     */
    public function getDtiPermitUrlAttribute(): ?string
    {
        if (!$this->dti_permit) {
            return null;
        }
        
        if (filter_var($this->dti_permit, FILTER_VALIDATE_URL)) {
            return $this->dti_permit;
        }
        
        return asset('storage/dti-permits/' . $this->dti_permit);
    }

    /**
     * Check if user is owner
     */
    public function isOwner(): bool
    {
        return $this->user_type === 'Owner';
    }

    /**
     * Check if user is tenant
     */
    public function isTenant(): bool
    {
        return $this->user_type === 'Tenant';
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->user_type === 'Admin';
    }

    /**
     * Check if user is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'Approved';
    }

    /**
     * Check if user has DTI permit (for owners)
     */
    public function hasDtiPermit(): bool
    {
        return !empty($this->dti_permit);
    }

    /**
     * Get user type with badge/color
     */
    public function getUserTypeBadgeAttribute(): array
    {
        $badges = [
            'Admin' => ['color' => 'danger', 'text' => 'Administrator'],
            'Owner' => ['color' => 'success', 'text' => 'Property Owner'],
            'Tenant' => ['color' => 'info', 'text' => 'Tenant']
        ];
        
        return $badges[$this->user_type] ?? ['color' => 'secondary', 'text' => $this->user_type];
    }

    /**
     * Get statistics for dashboard (for owners)
     */
    public function getOwnerStatisticsAttribute(): array
    {
        if (!$this->isOwner()) {
            return [];
        }
        
        $accommodationsCount = $this->accommodations()->count();
        $activeReservations = $this->ownerReservations()
            ->where('reservation_status', 'Approved')
            ->whereDate('end_date', '>=', now())
            ->count();
        
        $totalRevenue = $this->ownerReservations()
            ->where('reservation_status', 'Approved')
            ->whereYear('date_application', now()->year)
            ->get()
            ->sum(function($reservation) {
                $accommodation = $reservation->accommodation;
                if ($accommodation) {
                    $days = $reservation->calculateTotalDays();
                    return $days * ($accommodation->monthly_rate / 30);
                }
                return 0;
            });
        
        $occupancyRate = $accommodationsCount > 0 
            ? ($this->accommodations()->sum('total_beds') - $this->accommodations()->sum('available_beds')) / $this->accommodations()->sum('total_beds') * 100
            : 0;
        
        return [
            'accommodations_count' => $accommodationsCount,
            'active_reservations' => $activeReservations,
            'total_revenue' => $totalRevenue,
            'occupancy_rate' => round($occupancyRate, 2),
            'pending_reservations' => $this->ownerReservations()
                ->where('reservation_status', 'Pending')
                ->count(),
        ];
    }

    /**
     * Get statistics for dashboard (for tenants)
     */
    public function getTenantStatisticsAttribute(): array
    {
        if (!$this->isTenant()) {
            return [];
        }
        
        return [
            'total_reservations' => $this->tenantReservations()->count(),
            'active_reservations' => $this->tenantReservations()
                ->where('reservation_status', 'Approved')
                ->whereDate('end_date', '>=', now())
                ->count(),
            'pending_reservations' => $this->tenantReservations()
                ->where('reservation_status', 'Pending')
                ->count(),
            'completed_reservations' => $this->tenantReservations()
                ->where('reservation_status', 'Approved')
                ->whereDate('end_date', '<', now())
                ->count(),
        ];
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadNotificationsCountAttribute(): int
    {
        return $this->receivedNotifications()
            ->where('seen', 'unsee')
            ->count();
    }

    /**
     * Get recent notifications
     */
    public function getRecentNotificationsAttribute($limit = 5)
    {
        return $this->receivedNotifications()
            ->orderBy('date_sent', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user's accommodations with availability
     */
    public function getAccommodationsWithAvailabilityAttribute()
    {
        if (!$this->isOwner()) {
            return collect();
        }
        
        return $this->accommodations()->with(['images'])->get()->map(function($accommodation) {
            return [
                'id' => $accommodation->accomodation_id,
                'name' => $accommodation->room_name,
                'type' => $accommodation->accomodation_type,
                'status' => $accommodation->status,
                'available_beds' => $accommodation->available_beds,
                'total_beds' => $accommodation->total_beds,
                'occupancy_percentage' => $accommodation->occupancy_percentage,
                'monthly_rate' => $accommodation->monthly_rate,
                'featured_image' => $accommodation->featured_image ? $accommodation->featured_image->image_url : null,
            ];
        });
    }

    /**
     * Update last seen/view
     */
    public function updateLastSeen(): void
    {
        $this->view = 'Yes';
        $this->save();
    }

    /**
     * Check if user can create accommodation
     */
    public function canCreateAccommodation(): bool
    {
        return $this->isOwner() && $this->isApproved() && $this->hasDtiPermit();
    }

    /**
     * Get verification status
     */
    public function getVerificationStatusAttribute(): string
    {
        if ($this->isOwner() && $this->hasDtiPermit()) {
            return 'Verified Owner';
        } elseif ($this->isOwner()) {
            return 'Unverified Owner';
        } elseif ($this->isTenant()) {
            return 'Verified Tenant';
        } else {
            return $this->user_type;
        }
    }

    /**
     * Get gender from name (simple detection for demo)
     */
    public function getGenderAttribute(): string
    {
        // This is a simple implementation - in real app, you'd store gender separately
        $femaleNames = ['Maria', 'Ana', 'Mary', 'Jane', 'Catherine', 'Elizabeth', 'Susan', 'Margaret'];
        $maleNames = ['Juan', 'John', 'Michael', 'David', 'James', 'Robert', 'William', 'Joseph'];
        
        $firstName = strtolower($this->firstname);
        
        foreach ($femaleNames as $name) {
            if (str_contains($firstName, strtolower($name))) {
                return 'Female';
            }
        }
        
        foreach ($maleNames as $name) {
            if (str_contains($firstName, strtolower($name))) {
                return 'Male';
            }
        }
        
        return 'Unknown';
    }
}