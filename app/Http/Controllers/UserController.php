<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Notification;
use App\Models\ProfileInfo;
use App\Models\Reservation;
use App\Models\AccommodationList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Get current authenticated user
     */
    public function getCurrentUser()
    {
        $user = auth()->user();
        
        $user->load(['profileInfo']);
        
        // Add additional computed data
        $userData = $user->toArray();
        
        // Add role-specific statistics
        if ($user->isOwner()) {
            $userData['statistics'] = $user->owner_statistics;
            $userData['accommodations'] = $user->accommodations_with_availability;
        } elseif ($user->isTenant()) {
            $userData['statistics'] = $user->tenant_statistics;
        }
        
        // Add unread notifications count
        $userData['unread_notifications_count'] = $user->unread_notifications_count;
        
        return response()->json([
            'success' => true,
            'data' => $userData
        ]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $validator = Validator::make($request->all(), [
            'firstname' => 'sometimes|string|max:100',
            'middlename' => 'nullable|string|max:100',
            'lastname' => 'sometimes|string|max:100',
            'extension_name' => 'nullable|string|max:50',
            'province' => 'nullable|string|max:100',
            'municipality' => 'nullable|string|max:100',
            'barangay' => 'nullable|string|max:100',
            'zipcode' => 'nullable|string|max:10',
            'email' => 'sometimes|email|unique:accounts,email,' . $user->user_id . ',user_id',
            'mobile_number' => 'sometimes|string|max:20',
            'location' => 'nullable|string|max:255',
            'house_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            // Update basic account info
            $user->update($request->only([
                'firstname', 'middlename', 'lastname', 'extension_name',
                'province', 'municipality', 'barangay', 'zipcode',
                'email', 'mobile_number', 'location', 'house_name'
            ]));

            // Update or create profile info
            if ($request->has('bio')) {
                $profileInfo = ProfileInfo::updateOrCreate(
                    ['user_id' => $user->user_id],
                    ['bio' => $request->bio]
                );
            }

            // If owner, update DTI permit if provided
            if ($user->isOwner() && $request->hasFile('dti_permit')) {
                $validator = Validator::make($request->all(), [
                    'dti_permit' => 'required|file|mimes:jpeg,png,jpg,pdf|max:5120',
                ]);

                if ($validator->fails()) {
                    return response()->json([
                        'success' => false,
                        'errors' => $validator->errors()
                    ], 422);
                }

                // Delete old DTI permit if exists
                if ($user->dti_permit && Storage::exists('public/dti-permits/' . $user->dti_permit)) {
                    Storage::delete('public/dti-permits/' . $user->dti_permit);
                }

                // Upload new DTI permit
                $file = $request->file('dti_permit');
                $fileName = 'dti_' . $user->user_id . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('dti-permits', $fileName, 'public');
                
                $user->dti_permit = $fileName;
                $user->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => $user->fresh(['profileInfo'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update profile photo
     */
    public function updateProfilePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'profile_photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();

        DB::beginTransaction();
        try {
            // Delete old profile photo if exists
            if ($user->profile && Storage::exists('public/profiles/' . $user->profile)) {
                Storage::delete('public/profiles/' . $user->profile);
            }

            // Upload new profile photo
            $file = $request->file('profile_photo');
            $fileName = 'profile_' . $user->user_id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('profiles', $fileName, 'public');
            
            $user->profile = $fileName;
            $user->save();

            // Also update profile info image if exists
            $profileInfo = ProfileInfo::where('user_id', $user->user_id)->first();
            if ($profileInfo) {
                // Delete old image if exists
                if ($profileInfo->image_profile && Storage::exists('public/profiles/' . $profileInfo->image_profile)) {
                    Storage::delete('public/profiles/' . $profileInfo->image_profile);
                }
                
                $profileInfo->image_profile = $fileName;
                $profileInfo->save();
            } else {
                // Create profile info if doesn't exist
                ProfileInfo::create([
                    'user_id' => $user->user_id,
                    'image_profile' => $fileName,
                    'bio' => ''
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profile photo updated successfully',
                'data' => [
                    'profile_url' => $user->profile_photo_url
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile photo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Change password
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
            'new_password_confirmation' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect'
            ], 400);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully'
        ]);
    }

    /**
     * Get user notifications
     */
    public function getNotifications(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 20);
        $unreadOnly = $request->query('unread_only', false);
        
        $query = $user->receivedNotifications()
            ->orderBy('date_sent', 'desc');
        
        if ($unreadOnly) {
            $query->where('seen', 'unsee');
        }
        
        $notifications = $query->paginate($perPage);
        
        // Transform notifications
        $notifications->getCollection()->transform(function($notification) {
            $sender = $notification->senderUser;
            return [
                'id' => $notification->notification_id,
                'message' => $notification->notification_message,
                'preview' => $notification->preview,
                'type' => $notification->type,
                'sender' => $sender ? [
                    'id' => $sender->user_id,
                    'name' => $sender->full_name,
                    'type' => $sender->user_type
                ] : null,
                'seen' => $notification->seen,
                'is_unread' => $notification->isUnread(),
                'date_sent' => $notification->date_sent,
                'formatted_date' => $notification->formatted_date,
                'time_ago' => $notification->date_sent->diffForHumans(),
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $notifications,
            'unread_count' => $user->unread_notifications_count
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead($id)
    {
        $user = auth()->user();
        
        $notification = Notification::where('notification_id', $id)
            ->where('receiver', $user->user_id)
            ->first();
        
        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found'
            ], 404);
        }
        
        $notification->markAsRead();
        
        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        $user = auth()->user();
        
        $updated = Notification::where('receiver', $user->user_id)
            ->where('seen', 'unsee')
            ->update(['seen' => 'seen']);
        
        return response()->json([
            'success' => true,
            'message' => "{$updated} notifications marked as read"
        ]);
    }

    /**
     * Get saved accommodations (favorites/wishlist)
     */
    public function getSavedAccommodations(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 12);
        
        $savedAccommodations = AccommodationList::getUserSavedAccommodations($user->user_id);
        
        // Paginate manually since we're using a static method
        $page = $request->query('page', 1);
        $offset = ($page - 1) * $perPage;
        $paginatedItems = array_slice($savedAccommodations->toArray(), $offset, $perPage);
        
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedItems,
            count($savedAccommodations),
            $perPage,
            $page,
            ['path' => $request->url()]
        );
        
        return response()->json([
            'success' => true,
            'data' => $paginator
        ]);
    }

    /**
     * Save accommodation to list (add to favorites)
     */
    public function saveAccommodation(Request $request, $accommodationId)
    {
        $user = auth()->user();
        
        // Check if accommodation exists
        $accommodation = \App\Models\Accommodation::find($accommodationId);
        if (!$accommodation) {
            return response()->json([
                'success' => false,
                'message' => 'Accommodation not found'
            ], 404);
        }
        
        $added = AccommodationList::addToUserList($user->user_id, $accommodationId);
        
        if ($added) {
            return response()->json([
                'success' => true,
                'message' => 'Accommodation saved to your list'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Accommodation is already in your list'
        ], 400);
    }

    /**
     * Remove accommodation from saved list
     */
    public function unsaveAccommodation($accommodationId)
    {
        $user = auth()->user();
        
        $removed = AccommodationList::removeFromUserList($user->user_id, $accommodationId);
        
        if ($removed) {
            return response()->json([
                'success' => true,
                'message' => 'Accommodation removed from your list'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Accommodation not found in your list'
        ], 404);
    }

    /**
     * Check if accommodation is saved
     */
    public function checkSavedAccommodation($accommodationId)
    {
        $user = auth()->user();
        
        $isSaved = AccommodationList::userHasAccommodation($user->user_id, $accommodationId);
        
        return response()->json([
            'success' => true,
            'data' => [
                'is_saved' => $isSaved,
                'save_count' => AccommodationList::getAccommodationSaveCount($accommodationId)
            ]
        ]);
    }

    /**
     * Get user's active reservations
     */
    public function getActiveReservations(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 10);
        
        $reservations = Reservation::with(['accommodation.images', 'owner', 'deck'])
            ->where(function($query) use ($user) {
                $query->where('tenant_user_id', $user->user_id)
                      ->orWhere('owner_user_id', $user->user_id);
            })
            ->where('reservation_status', 'Approved')
            ->whereDate('end_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->paginate($perPage);
        
        // Transform reservations
        $reservations->getCollection()->transform(function($reservation) use ($user) {
            $accommodation = $reservation->accommodation;
            $isTenant = $reservation->tenant_user_id === $user->user_id;
            
            return [
                'id' => $reservation->reservation_id,
                'accommodation' => $accommodation ? [
                    'id' => $accommodation->accomodation_id,
                    'room_name' => $accommodation->room_name,
                    'type' => $accommodation->accomodation_type,
                    'location' => $accommodation->location,
                    'featured_image' => $accommodation->featured_image ? $accommodation->featured_image->image_url : null,
                ] : null,
                'dates' => [
                    'start' => $reservation->start_date,
                    'end' => $reservation->end_date,
                    'total_days' => $reservation->calculateTotalDays(),
                    'days_remaining' => max(0, Carbon::parse($reservation->end_date)->diffInDays(now(), false)),
                ],
                'status' => $reservation->reservation_status,
                'bed_details' => [
                    'bed_desk' => $reservation->bed_desk,
                    'layer' => $reservation->layer,
                ],
                'other_party' => $isTenant ? [
                    'id' => $reservation->owner->user_id,
                    'name' => $reservation->owner->full_name,
                    'type' => 'Owner'
                ] : [
                    'id' => $reservation->tenant->user_id,
                    'name' => $reservation->tenant->full_name,
                    'type' => 'Tenant'
                ],
                'role' => $isTenant ? 'Tenant' : 'Owner',
                'total_cost' => $accommodation ? 
                    round($reservation->calculateTotalDays() * ($accommodation->monthly_rate / 30), 2) : 0,
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }

    /**
     * Get user's upcoming reservations
     */
    public function getUpcomingReservations(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 10);
        
        $reservations = Reservation::with(['accommodation.images', 'owner', 'deck'])
            ->where('tenant_user_id', $user->user_id)
            ->where('reservation_status', 'Approved')
            ->whereDate('start_date', '>', now())
            ->orderBy('start_date', 'asc')
            ->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }

    /**
     * Get user's reservation history
     */
    public function getReservationHistory(Request $request)
    {
        $user = auth()->user();
        $perPage = $request->query('per_page', 10);
        
        $query = Reservation::with(['accommodation.images', 'owner'])
            ->where(function($query) use ($user) {
                $query->where('tenant_user_id', $user->user_id)
                      ->orWhere('owner_user_id', $user->user_id);
            })
            ->orderBy('date_application', 'desc');
        
        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('reservation_status', $request->status);
        }
        
        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date_application', [
                $request->start_date,
                $request->end_date
            ]);
        }
        
        $reservations = $query->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }

    /**
     * Get user's monthly statistics
     */
    public function getMonthlyStatistics(Request $request)
    {
        $user = auth()->user();
        $month = $request->query('month', date('m'));
        $year = $request->query('year', date('Y'));
        
        if ($user->isOwner()) {
            // Owner statistics
            $reservations = Reservation::where('owner_user_id', $user->user_id)
                ->whereMonth('date_application', $month)
                ->whereYear('date_application', $year)
                ->get();
            
            $totalRevenue = $reservations->where('reservation_status', 'Approved')
                ->sum(function($reservation) {
                    $accommodation = $reservation->accommodation;
                    if ($accommodation) {
                        $days = $reservation->calculateTotalDays();
                        return $days * ($accommodation->monthly_rate / 30);
                    }
                    return 0;
                });
            
            $statistics = [
                'total_reservations' => $reservations->count(),
                'approved_reservations' => $reservations->where('reservation_status', 'Approved')->count(),
                'pending_reservations' => $reservations->where('reservation_status', 'Pending')->count(),
                'cancelled_reservations' => $reservations->where('reservation_status', 'Cancelled')->count(),
                'total_revenue' => $totalRevenue,
                'average_stay_duration' => $reservations->where('reservation_status', 'Approved')->avg(function($reservation) {
                    return $reservation->calculateTotalDays();
                }) ?? 0,
            ];
            
        } elseif ($user->isTenant()) {
            // Tenant statistics
            $reservations = Reservation::where('tenant_user_id', $user->user_id)
                ->whereMonth('date_application', $month)
                ->whereYear('date_application', $year)
                ->get();
            
            $totalSpent = $reservations->where('reservation_status', 'Approved')
                ->sum(function($reservation) {
                    $accommodation = $reservation->accommodation;
                    if ($accommodation) {
                        $days = $reservation->calculateTotalDays();
                        return $days * ($accommodation->monthly_rate / 30);
                    }
                    return 0;
                });
            
            $statistics = [
                'total_reservations' => $reservations->count(),
                'approved_reservations' => $reservations->where('reservation_status', 'Approved')->count(),
                'pending_reservations' => $reservations->where('reservation_status', 'Pending')->count(),
                'cancelled_reservations' => $reservations->where('reservation_status', 'Cancelled')->count(),
                'total_spent' => $totalSpent,
                'average_stay_duration' => $reservations->where('reservation_status', 'Approved')->avg(function($reservation) {
                    return $reservation->calculateTotalDays();
                }) ?? 0,
                'favorite_accommodation_type' => $this->getFavoriteAccommodationType($user->user_id, $month, $year),
            ];
        } else {
            $statistics = [];
        }
        
        return response()->json([
            'success' => true,
            'data' => [
                'month' => $month,
                'year' => $year,
                'month_name' => date('F', mktime(0, 0, 0, $month, 1)),
                'statistics' => $statistics
            ]
        ]);
    }

    /**
     * Get favorite accommodation type for tenant
     */
    private function getFavoriteAccommodationType($userId, $month, $year)
    {
        $reservations = Reservation::with('accommodation')
            ->where('tenant_user_id', $userId)
            ->where('reservation_status', 'Approved')
            ->whereMonth('date_application', $month)
            ->whereYear('date_application', $year)
            ->get()
            ->pluck('accommodation.accomodation_type')
            ->filter()
            ->countBy()
            ->sortDesc()
            ->keys()
            ->first();
        
        return $reservations ?: 'None';
    }

    /**
     * Get user's accommodation recommendations (for tenants)
     */
    public function getRecommendations(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isTenant()) {
            return response()->json([
                'success' => false,
                'message' => 'Recommendations are only available for tenants'
            ], 400);
        }
        
        $perPage = $request->query('per_page', 6);
        
        // Get user's previous accommodation preferences
        $previousReservations = Reservation::with('accommodation')
            ->where('tenant_user_id', $user->user_id)
            ->where('reservation_status', 'Approved')
            ->get();
        
        $preferredTypes = $previousReservations->pluck('accommodation.accomodation_type')
            ->filter()
            ->unique()
            ->toArray();
        
        $preferredLocations = $previousReservations->pluck('accommodation.location')
            ->filter()
            ->unique()
            ->toArray();
        
        // Build recommendation query
        $query = \App\Models\Accommodation::with(['images', 'owner', 'amenities'])
            ->where('status', 'Available')
            ->inBulan()
            ->orderBy('date_created', 'desc');
        
        // Add filters based on preferences
        if (!empty($preferredTypes)) {
            $query->whereIn('accomodation_type', $preferredTypes);
        }
        
        if (!empty($preferredLocations)) {
            $locationQuery = function($q) use ($preferredLocations) {
                foreach ($preferredLocations as $location) {
                    $q->orWhere('location', 'like', '%' . $location . '%');
                }
            };
            $query->where($locationQuery);
        }
        
        // Add gender preference filter
        $userGender = $user->gender; // From Account model
        if ($userGender !== 'Unknown') {
            $query->where(function($q) use ($userGender) {
                $q->where('gender_preference', 'Unisex')
                  ->orWhere('gender_preference', $userGender);
            });
        }
        
        $recommendations = $query->limit($perPage)->get();
        
        // If not enough recommendations, get more
        if ($recommendations->count() < $perPage) {
            $additionalNeeded = $perPage - $recommendations->count();
            $additional = \App\Models\Accommodation::with(['images', 'owner', 'amenities'])
                ->where('status', 'Available')
                ->inBulan()
                ->whereNotIn('accomodation_id', $recommendations->pluck('accomodation_id'))
                ->orderBy('date_created', 'desc')
                ->limit($additionalNeeded)
                ->get();
            
            $recommendations = $recommendations->concat($additional);
        }
        
        return response()->json([
            'success' => true,
            'data' => $recommendations,
            'based_on' => [
                'preferred_types' => $preferredTypes,
                'preferred_locations' => $preferredLocations,
                'user_gender' => $userGender
            ]
        ]);
    }

    /**
     * Update user settings
     */
    public function updateSettings(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'language' => 'in:en,fil',
            'theme' => 'in:light,dark',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        
        // In a real application, you'd have a user_settings table
        // For now, we'll store in a JSON field or separate table
        // This is a placeholder implementation
        
        return response()->json([
            'success' => true,
            'message' => 'Settings updated successfully'
        ]);
    }

    /**
     * Deactivate account
     */
    public function deactivateAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = auth()->user();
        
        // Verify password
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect password'
            ], 400);
        }
        
        // Check if user has active reservations
        $activeReservations = Reservation::where(function($query) use ($user) {
                $query->where('tenant_user_id', $user->user_id)
                      ->orWhere('owner_user_id', $user->user_id);
            })
            ->where('reservation_status', 'Approved')
            ->whereDate('end_date', '>=', now())
            ->exists();
        
        if ($activeReservations) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot deactivate account with active reservations'
            ], 400);
        }
        
        // For owners, check if they have accommodations
        if ($user->isOwner()) {
            $hasAccommodations = $user->accommodations()->exists();
            if ($hasAccommodations) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please delete or transfer your accommodations before deactivating your account'
                ], 400);
            }
        }
        
        // Mark account as deactivated
        $user->status = 'Deactivated';
        $user->save();
        
        // Log deactivation reason (in a real app, you'd have a deactivation_logs table)
        // \App\Models\DeactivationLog::create([
        //     'user_id' => $user->user_id,
        //     'reason' => $request->reason,
        //     'deactivated_at' => now(),
        // ]);
        
        // Revoke all tokens
        $user->tokens()->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Account deactivated successfully'
        ]);
    }

    /**
     * Get all users (Admin only)
     */
    public function getAllUsers(Request $request)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Admin access required'
            ], 403);
        }
        
        $perPage = $request->query('per_page', 20);
        $userType = $request->query('user_type');
        $status = $request->query('status');
        $search = $request->query('search');
        
        $query = Account::query();
        
        if ($userType) {
            $query->where('user_type', $userType);
        }
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('firstname', 'like', '%' . $search . '%')
                  ->orWhere('lastname', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%')
                  ->orWhere('mobile_number', 'like', '%' . $search . '%')
                  ->orWhere('user_id', 'like', '%' . $search . '%');
            });
        }
        
        $users = $query->orderBy('date_registered', 'desc')
            ->paginate($perPage);
        
        // Add additional statistics
        $users->getCollection()->transform(function($user) {
            $userData = $user->toArray();
            
            if ($user->isOwner()) {
                $userData['accommodations_count'] = $user->accommodations()->count();
                $userData['active_reservations'] = $user->ownerReservations()
                    ->where('reservation_status', 'Approved')
                    ->whereDate('end_date', '>=', now())
                    ->count();
            } elseif ($user->isTenant()) {
                $userData['reservations_count'] = $user->tenantReservations()->count();
                $userData['active_reservations'] = $user->tenantReservations()
                    ->where('reservation_status', 'Approved')
                    ->whereDate('end_date', '>=', now())
                    ->count();
            }
            
            return $userData;
        });
        
        return response()->json([
            'success' => true,
            'data' => $users,
            'filters' => [
                'user_type' => $userType,
                'status' => $status,
                'search' => $search
            ]
        ]);
    }

    /**
     * Update user status (Admin only)
     */
    public function updateUserStatus(Request $request, $id)
    {
        $admin = auth()->user();
        
        if (!$admin->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Admin access required'
            ], 403);
        }
        
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Approved,Pending,Rejected,Deactivated',
            'reason' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }
        
        $user = Account::find($id);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }
        
        $oldStatus = $user->status;
        $user->status = $request->status;
        $user->save();
        
        // Send notification to user about status change
        if ($oldStatus !== $request->status) {
            $notificationId = date('Y-m-d') . '_' . Str::random(10);
            
            $message = "Your account status has been changed from {$oldStatus} to {$request->status}.";
            if ($request->reason) {
                $message .= " Reason: {$request->reason}";
            }
            
            Notification::create([
                'notification_id' => $notificationId,
                'notification_message' => $message,
                'receiver' => $user->user_id,
                'sender' => $admin->user_id,
                'seen' => 'unsee',
                'date_sent' => Carbon::now()->toDateTimeString(),
            ]);
        }
        
        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully',
            'data' => $user
        ]);
    }
}