<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Accommodation;
use App\Models\Account;
use App\Models\Notification;
use App\Models\Occupant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // This would typically be admin-only
        $reservations = Reservation::with(['tenant', 'owner', 'accommodation'])
            ->orderBy('date_application', 'desc')
            ->paginate(20);
            
        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'accommodation_id' => 'required|exists:accomodations,accomodation_id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'bed_desk' => 'nullable|string',
            'layer' => 'nullable|string',
            'deck_id' => 'required|exists:deck_images,deck_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Check accommodation availability
        $accommodation = Accommodation::find($request->accommodation_id);
        
        if (!$accommodation || $accommodation->status === 'Occupied') {
            return response()->json([
                'success' => false,
                'message' => 'Accommodation is not available'
            ], 400);
        }

        // Check for existing reservations on the same bed/deck
        $existingReservation = Reservation::where('deck_id', $request->deck_id)
            ->where('accomodation_id', $request->accommodation_id)
            ->where('reservation_status', 'Approved')
            ->where(function($query) use ($request) {
                $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                      ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                      ->orWhere(function($q) use ($request) {
                          $q->where('start_date', '<=', $request->start_date)
                            ->where('end_date', '>=', $request->end_date);
                      });
            })
            ->first();

        if ($existingReservation) {
            return response()->json([
                'success' => false,
                'message' => 'Selected bed/space is already reserved for the selected dates'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $user = Auth::user();
            $reservationId = 'res_' . Str::random(14);
            $expirationDate = Carbon::now()->addDays(2); // 2 days to confirm
            
            $reservation = Reservation::create([
                'reservation_id' => $reservationId,
                'tenant_user_id' => $user->user_id,
                'reservation_status' => 'Pending',
                'notification_status' => 'unsee',
                'accomodation_id' => $request->accommodation_id,
                'owner_user_id' => $accommodation->user_id,
                'date_application' => Carbon::now()->toDateString(),
                'time_application' => Carbon::now()->format('h:i:s a'),
                'hide_status' => 'show',
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'bed_desk' => $request->bed_desk,
                'layer' => $request->layer,
                'status_user' => 'pending',
                'date_expiration' => $expirationDate->toDateString(),
                'time_expiration' => $expirationDate->format('h:i:s a'),
                'deck_id' => $request->deck_id,
            ]);

            // Create notification for owner
            $notificationId = Carbon::now()->format('Y-m-d') . '_' . Str::random(10);
            
            Notification::create([
                'notification_id' => $notificationId,
                'notification_message' => "New reservation request from {$user->firstname} {$user->lastname} for {$accommodation->room_name}",
                'receiver' => $accommodation->user_id,
                'sender' => $user->user_id,
                'seen' => 'unsee',
                'date_sent' => Carbon::now()->toDateTimeString(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservation request submitted successfully',
                'data' => $reservation->load(['accommodation', 'deck'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create reservation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $reservation = Reservation::with(['tenant', 'owner', 'accommodation', 'deck'])
            ->where('reservation_id', $id)
            ->first();

        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found'
            ], 404);
        }

        // Check authorization
        if ($reservation->tenant_user_id !== $user->user_id && 
            $reservation->owner_user_id !== $user->user_id &&
            $user->user_type !== 'Admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view this reservation'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $reservation
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $reservation = Reservation::find($id);
        
        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found'
            ], 404);
        }

        $user = Auth::user();
        
        // Check if user is the owner of the accommodation
        if ($reservation->owner_user_id !== $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this reservation'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'reservation_status' => 'required|in:Approved,Denied,Cancelled',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $reservation->reservation_status;
            $reservation->reservation_status = $request->reservation_status;
            $reservation->save();

            // If approved, create occupant record
            if ($request->reservation_status === 'Approved' && $oldStatus !== 'Approved') {
                $occupantId = Carbon::now()->format('Y-m-d') . '_' . Str::random(10);
                
                Occupant::create([
                    'occupants_id' => $occupantId,
                    'tenant_id' => $reservation->tenant_user_id,
                    'owner_id' => $reservation->owner_user_id,
                    'accomodation_id' => $reservation->accomodation_id,
                    'reservation_id' => $reservation->reservation_id,
                    'status' => 'On Going',
                ]);

                // Update accommodation status if needed
                $accommodation = Accommodation::find($reservation->accomodation_id);
                if ($accommodation) {
                    $occupiedCount = Reservation::where('accomodation_id', $reservation->accomodation_id)
                        ->where('reservation_status', 'Approved')
                        ->whereDate('end_date', '>=', now())
                        ->count();
                    
                    if ($occupiedCount >= $accommodation->max_occupants) {
                        $accommodation->status = 'Occupied';
                    } else if ($occupiedCount > 0) {
                        $accommodation->status = 'Partially Occupied';
                    }
                    $accommodation->save();
                }
            }

            // Create notification for tenant
            $notificationId = Carbon::now()->format('Y-m-d') . '_' . Str::random(10);
            $statusMessage = $request->reservation_status === 'Approved' 
                ? 'approved' 
                : strtolower($request->reservation_status);
            
            $message = $request->reservation_status === 'Approved' 
                ? "I'm pleased to inform you that your accommodation request has been approved. We're happy to support your needs and ensure you have a comfortable experience.\nReminder that if 2 days you're not arrived the room will be available.\n\nIf you have any further questions or require additional assistance, please don't hesitate to reach out."
                : "Your accommodation request has been {$statusMessage}.";

            Notification::create([
                'notification_id' => $notificationId,
                'notification_message' => $message,
                'receiver' => $reservation->tenant_user_id,
                'sender' => $user->user_id,
                'seen' => 'unsee',
                'date_sent' => Carbon::now()->toDateTimeString(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservation status updated successfully',
                'data' => $reservation->fresh(['tenant', 'accommodation'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update reservation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cancel a reservation (tenant action).
     */
    public function cancel(Request $request, $id)
    {
        $reservation = Reservation::find($id);
        
        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found'
            ], 404);
        }

        $user = Auth::user();
        
        // Check if user is the tenant who made the reservation
        if ($reservation->tenant_user_id !== $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to cancel this reservation'
            ], 403);
        }

        // Check if reservation can be cancelled
        if ($reservation->reservation_status !== 'Pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending reservations can be cancelled'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $reservation->reservation_status = 'Cancelled';
            $reservation->save();

            // Create notification for owner
            $notificationId = Carbon::now()->format('Y-m-d') . '_' . Str::random(10);
            
            Notification::create([
                'notification_id' => $notificationId,
                'notification_message' => "Reservation {$reservation->reservation_id} has been cancelled by {$user->firstname} {$user->lastname}",
                'receiver' => $reservation->owner_user_id,
                'sender' => $user->user_id,
                'seen' => 'unsee',
                'date_sent' => Carbon::now()->toDateTimeString(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservation cancelled successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel reservation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Extend reservation period.
     */
    public function extend(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'new_end_date' => 'required|date|after:today',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $reservation = Reservation::find($id);
        
        if (!$reservation) {
            return response()->json([
                'success' => false,
                'message' => 'Reservation not found'
            ], 404);
        }

        $user = Auth::user();
        
        // Check if user is authorized
        if ($reservation->tenant_user_id !== $user->user_id && $user->user_type !== 'Admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to extend this reservation'
            ], 403);
        }

        // Check if reservation can be extended
        if ($reservation->reservation_status !== 'Approved') {
            return response()->json([
                'success' => false,
                'message' => 'Only approved reservations can be extended'
            ], 400);
        }

        if (Carbon::parse($request->new_end_date)->lte($reservation->end_date)) {
            return response()->json([
                'success' => false,
                'message' => 'New end date must be after current end date'
            ], 400);
        }

        // Check bed availability for extended period
        $existingReservation = Reservation::where('deck_id', $reservation->deck_id)
            ->where('accomodation_id', $reservation->accomodation_id)
            ->where('reservation_id', '!=', $reservation->reservation_id)
            ->where('reservation_status', 'Approved')
            ->where(function($query) use ($reservation, $request) {
                $query->whereBetween('start_date', [$reservation->end_date, $request->new_end_date])
                      ->orWhereBetween('end_date', [$reservation->end_date, $request->new_end_date]);
            })
            ->first();

        if ($existingReservation) {
            return response()->json([
                'success' => false,
                'message' => 'Bed/space is already reserved for the extended period'
            ], 400);
        }

        DB::beginTransaction();
        try {
            $oldEndDate = $reservation->end_date;
            $reservation->end_date = $request->new_end_date;
            $reservation->save();

            // Create notification for owner
            $notificationId = Carbon::now()->format('Y-m-d') . '_' . Str::random(10);
            
            Notification::create([
                'notification_id' => $notificationId,
                'notification_message' => "Reservation {$reservation->reservation_id} has been extended from {$oldEndDate} to {$request->new_end_date}",
                'receiver' => $reservation->owner_user_id,
                'sender' => $user->user_id,
                'seen' => 'unsee',
                'date_sent' => Carbon::now()->toDateTimeString(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Reservation extended successfully',
                'data' => $reservation
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to extend reservation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's reservations.
     */
    public function getUserReservations(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');
        $perPage = $request->query('per_page', 10);
        
        $query = Reservation::with(['accommodation', 'owner', 'deck'])
            ->where('tenant_user_id', $user->user_id)
            ->orderBy('date_application', 'desc');

        if ($status) {
            $query->where('reservation_status', $status);
        }

        $reservations = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }

    /**
     * Get owner's reservations.
     */
    public function getOwnerReservations(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');
        $perPage = $request->query('per_page', 10);
        
        $query = Reservation::with(['tenant', 'accommodation', 'deck'])
            ->where('owner_user_id', $user->user_id)
            ->orderBy('date_application', 'desc');

        if ($status) {
            $query->where('reservation_status', $status);
        }

        $reservations = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $reservations
        ]);
    }

    /**
     * Get occupancy statistics.
     */
    public function getOccupancyStatistics(Request $request)
    {
        $user = Auth::user();
        
        if ($user->user_type !== 'Owner') {
            return response()->json([
                'success' => false,
                'message' => 'Only owners can view occupancy statistics'
            ], 403);
        }

        $month = $request->query('month', date('m'));
        $year = $request->query('year', date('Y'));

        $statistics = Reservation::selectRaw('
            COUNT(*) as total_reservations,
            SUM(CASE WHEN reservation_status = "Approved" THEN 1 ELSE 0 END) as approved_reservations,
            SUM(CASE WHEN reservation_status = "Pending" THEN 1 ELSE 0 END) as pending_reservations,
            SUM(CASE WHEN reservation_status = "Cancelled" THEN 1 ELSE 0 END) as cancelled_reservations,
            AVG(DATEDIFF(end_date, start_date)) as average_stay_duration
        ')
        ->where('owner_user_id', $user->user_id)
        ->whereMonth('date_application', $month)
        ->whereYear('date_application', $year)
        ->first();

        return response()->json([
            'success' => true,
            'data' => $statistics
        ]);
    }
}