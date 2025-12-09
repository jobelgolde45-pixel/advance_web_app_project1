<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reservation';
    protected $primaryKey = 'reservation_id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'reservation_id',
        'tenant_user_id',
        'reservation_status',
        'notification_status',
        'accomodation_id',
        'owner_user_id',
        'date_application',
        'time_application',
        'hide_status',
        'start_date',
        'end_date',
        'time_duration',
        'bed_desk',
        'layer',
        'status_user',
        'date_expiration',
        'time_expiration',
        'deck_id'
    ];

    protected $casts = [
        'date_application' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'date_expiration' => 'date',
    ];

    /**
     * Relationship with tenant
     */
    public function tenant()
    {
        return $this->belongsTo(Account::class, 'tenant_user_id', 'user_id')
            ->where('user_type', 'Tenant');
    }

    /**
     * Relationship with owner
     */
    public function owner()
    {
        return $this->belongsTo(Account::class, 'owner_user_id', 'user_id')
            ->where('user_type', 'Owner');
    }

    /**
     * Relationship with accommodation
     */
    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class, 'accomodation_id', 'accomodation_id');
    }

    /**
     * Relationship with deck
     */
    public function deck()
    {
        return $this->belongsTo(DeckImage::class, 'deck_id', 'deck_id');
    }

    /**
     * Check if reservation is active
     */
    public function isActive(): bool
    {
        return $this->reservation_status === 'Approved' 
            && $this->status_user !== 'abandoned'
            && now()->lessThanOrEqualTo($this->end_date);
    }

    /**
     * Check if reservation is pending
     */
    public function isPending(): bool
    {
        return $this->reservation_status === 'Pending';
    }

    /**
     * Check if reservation is expired
     */
    public function isExpired(): bool
    {
        return now()->greaterThan($this->date_expiration) 
            && $this->reservation_status !== 'Approved';
    }

    /**
     * Calculate total days
     */
    public function calculateTotalDays(): int
    {
        $start = \Carbon\Carbon::parse($this->start_date);
        $end = \Carbon\Carbon::parse($this->end_date);
        return $start->diffInDays($end);
    }

    /**
     * Scope for active reservations
     */
    public function scopeActive($query)
    {
        return $query->where('reservation_status', 'Approved')
            ->where('status_user', '!=', 'abandoned')
            ->whereDate('end_date', '>=', now()->toDateString());
    }

    /**
     * Scope for pending reservations
     */
    public function scopePending($query)
    {
        return $query->where('reservation_status', 'Pending')
            ->whereDate('date_expiration', '>=', now()->toDateString());
    }

    /**
     * Scope for owner's reservations
     */
    public function scopeForOwner($query, $ownerId)
    {
        return $query->where('owner_user_id', $ownerId);
    }

    /**
     * Scope for tenant's reservations
     */
    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_user_id', $tenantId);
    }
}