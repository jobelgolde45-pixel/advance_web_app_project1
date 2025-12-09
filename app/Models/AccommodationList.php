<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccommodationList extends Model
{
    use HasFactory;

    protected $table = 'accomodation_lists';
    protected $primaryKey = 'user_id_list';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id_list',
        'user_id',
        'accomodation_id'
    ];

    /**
     * Relationship with user
     */
    public function user()
    {
        return $this->belongsTo(Account::class, 'user_id', 'user_id');
    }

    /**
     * Relationship with accommodation
     */
    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class, 'accomodation_id', 'accomodation_id');
    }

    /**
     * Scope for user's favorites/saved accommodations
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for accommodation in user's list
     */
    public function scopeForAccommodation($query, $accommodationId)
    {
        return $query->where('accomodation_id', $accommodationId);
    }

    /**
     * Check if user has accommodation in their list
     */
    public static function userHasAccommodation($userId, $accommodationId): bool
    {
        return self::forUser($userId)
            ->forAccommodation($accommodationId)
            ->exists();
    }

    /**
     * Add accommodation to user's list
     */
    public static function addToUserList($userId, $accommodationId): bool
    {
        if (self::userHasAccommodation($userId, $accommodationId)) {
            return false; // Already in list
        }

        $listId = date('Y-m-d') . '_' . mt_rand(10000, 99999);
        
        return self::create([
            'user_id_list' => $listId,
            'user_id' => $userId,
            'accomodation_id' => $accommodationId
        ]) !== null;
    }

    /**
     * Remove accommodation from user's list
     */
    public static function removeFromUserList($userId, $accommodationId): bool
    {
        return self::forUser($userId)
            ->forAccommodation($accommodationId)
            ->delete() > 0;
    }

    /**
     * Get user's saved accommodations with details
     */
    public static function getUserSavedAccommodations($userId)
    {
        return self::with(['accommodation.images', 'accommodation.owner', 'accommodation.amenities'])
            ->forUser($userId)
            ->get()
            ->map(function($item) {
                $accommodation = $item->accommodation;
                return [
                    'list_id' => $item->user_id_list,
                    'saved_date' => substr($item->user_id_list, 0, 10), // Extract date from ID
                    'accommodation' => $accommodation ? [
                        'id' => $accommodation->accomodation_id,
                        'room_name' => $accommodation->room_name,
                        'type' => $accommodation->accomodation_type,
                        'monthly_rate' => $accommodation->monthly_rate,
                        'formatted_rate' => $accommodation->formatted_monthly_rate,
                        'status' => $accommodation->status,
                        'location' => $accommodation->location,
                        'available_beds' => $accommodation->available_beds,
                        'total_beds' => $accommodation->total_beds,
                        'featured_image' => $accommodation->featured_image ? $accommodation->featured_image->image_url : null,
                        'owner_name' => $accommodation->owner ? $accommodation->owner->full_name : null,
                        'owner_house_name' => $accommodation->owner ? $accommodation->owner->house_name : null,
                        'amenities' => $accommodation->amenities->pluck('amenities_name'),
                    ] : null
                ];
            });
    }

    /**
     * Get accommodation save count
     */
    public static function getAccommodationSaveCount($accommodationId): int
    {
        return self::forAccommodation($accommodationId)->count();
    }

    /**
     * Get users who saved an accommodation
     */
    public static function getUsersWhoSavedAccommodation($accommodationId)
    {
        return self::with('user')
            ->forAccommodation($accommodationId)
            ->get()
            ->map(function($item) {
                return $item->user ? [
                    'user_id' => $item->user->user_id,
                    'name' => $item->user->full_name,
                    'email' => $item->user->email,
                    'saved_date' => substr($item->user_id_list, 0, 10),
                ] : null;
            })
            ->filter();
    }
}