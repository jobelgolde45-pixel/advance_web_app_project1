<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Occupant extends Model
{
    use HasFactory;

    protected $table = 'occupants';
    protected $primaryKey = 'occupants_id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'occupants_id',
        'tenant_id',
        'owner_id',
        'accomodation_id',
        'reservation_id',
        'status'
    ];

    /**
     * Relationship with tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Account::class, 'tenant_id', 'user_id');
    }

    /**
     * Relationship with owner
     */
    public function owner()
    {
        return $this->belongsTo(Account::class, 'owner_id', 'user_id');
    }

    /**
     * Relationship with accommodation
     */
    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class, 'accomodation_id', 'accomodation_id');
    }

    /**
     * Relationship with reservation
     */
    public function reservation()
    {
        return $this->belongsTo(Reservation::class, 'reservation_id', 'reservation_id');
    }

    /**
     * Scope for active occupants
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'On Going');
    }

    /**
     * Scope for occupants by accommodation
     */
    public function scopeByAccommodation($query, $accommodationId)
    {
        return $query->where('accomodation_id', $accommodationId);
    }

    /**
     * Scope for occupants by owner
     */
    public function scopeByOwner($query, $ownerId)
    {
        return $query->where('owner_id', $ownerId);
    }

    /**
     * Scope for occupants by tenant
     */
    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Check if occupant is active
     */
    public function isActive(): bool
    {
        return $this->status === 'On Going';
    }

    /**
     * Get stay duration
     */
    public function getStayDurationAttribute(): ?int
    {
        if ($this->reservation) {
            return $this->reservation->calculateTotalDays();
        }
        return null;
    }

    /**
     * Get days remaining
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if ($this->reservation && $this->isActive()) {
            $endDate = \Carbon\Carbon::parse($this->reservation->end_date);
            return max(0, now()->diffInDays($endDate, false));
        }
        return null;
    }

    /**
     * Check if stay is ending soon (within 3 days)
     */
    public function isEndingSoon(): bool
    {
        $daysRemaining = $this->days_remaining;
        return $daysRemaining !== null && $daysRemaining <= 3 && $daysRemaining > 0;
    }

    /**
     * Mark occupant as completed
     */
    public function markAsCompleted(): void
    {
        $this->status = 'Completed';
        $this->save();
    }

    /**
     * Get occupant information
     */
    public function getOccupantInfoAttribute(): array
    {
        $tenant = $this->tenant;
        $reservation = $this->reservation;
        
        return [
            'occupant_id' => $this->occupants_id,
            'tenant_name' => $tenant ? $tenant->full_name : 'Unknown',
            'tenant_email' => $tenant ? $tenant->email : null,
            'tenant_phone' => $tenant ? $tenant->mobile_number : null,
            'room_name' => $this->accommodation ? $this->accommodation->room_name : null,
            'accommodation_type' => $this->accommodation ? $this->accommodation->accomodation_type : null,
            'start_date' => $reservation ? $reservation->start_date : null,
            'end_date' => $reservation ? $reservation->end_date : null,
            'stay_duration' => $this->stay_duration,
            'days_remaining' => $this->days_remaining,
            'status' => $this->status,
            'is_active' => $this->isActive(),
            'is_ending_soon' => $this->isEndingSoon(),
        ];
    }

    /**
     * Get payment status (placeholder for future payment system)
     */
    public function getPaymentStatusAttribute(): string
    {
        // This would connect to a payments table if implemented
        // For now, return based on reservation status
        if ($this->reservation && $this->reservation->reservation_status === 'Approved') {
            return 'Paid'; // Assuming approved reservations are paid
        }
        
        return 'Pending';
    }
}