<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Accommodation;
use App\Models\Amenity;
use App\Models\RoomImage;
use App\Models\DeckImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccommodationController extends Controller
{
    /**
     * Display a listing of accommodations.
     */
    public function index(Request $request)
    {
        $query = Accommodation::with(['owner', 'images', 'amenities'])
            ->whereIn('status', ['Available', 'Partially Occupied'])
            ->orderBy('date_created', 'desc');

        // Apply filters
        if ($request->has('type')) {
            $query->where('accomodation_type', $request->type);
        }

        if ($request->has('gender_preference')) {
            $query->where('gender_preference', $request->gender_preference);
        }

        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('monthly_rate', [
                $request->min_price,
                $request->max_price
            ]);
        }

        if ($request->has('amenities')) {
            $amenityIds = explode(',', $request->amenities);
            $query->withAmenities($amenityIds);
        }

        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Show only accommodations in Bulan by default
        if (!$request->has('location')) {
            $query->inBulan();
        }

        $perPage = $request->get('per_page', 12);
        $accommodations = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $accommodations,
            'filters' => $request->all(),
            'total' => $accommodations->total()
        ]);
    }

    /**
     * Store a newly created accommodation.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'accomodation_type' => 'required|in:Boarding House,Lodging House,Dormitory,Apartment',
            'room_name' => 'required|string|max:255',
            'monthly_rate' => 'required|numeric|min:0',
            'max_occupants' => 'required|integer|min:1',
            'gender_preference' => 'required|in:Male,Female,Unisex',
            'total_beds' => 'required|integer|min:1',
            'description' => 'nullable|string',
            'location' => 'required|string',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,amenities_id',
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();

        DB::beginTransaction();
        try {
            $accommodationId = 'acc_' . Str::random(14);
            
            $accommodation = Accommodation::create([
                'accomodation_id' => $accommodationId,
                'accomodation_type' => $request->accomodation_type,
                'room_name' => $request->room_name,
                'monthly_rate' => $request->monthly_rate,
                'max_occupants' => $request->max_occupants,
                'gender_preference' => $request->gender_preference,
                'status' => 'Available',
                'total_beds' => $request->total_beds,
                'user_id' => $user->user_id,
                'description' => $request->description ?? '',
                'location' => $request->location,
                'date_created' => Carbon::now()->toDateString(),
                'date_updated' => Carbon::now()->toDateString(),
            ]);

            // Attach amenities
            if ($request->has('amenities')) {
                $accommodation->amenities()->attach($request->amenities);
            }

            // Upload images
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $imageId = 'img_' . Str::random(14);
                    $path = $image->store('accommodations', 'public');
                    
                    RoomImage::create([
                        'room_images_id' => $imageId,
                        'accomodation_id' => $accommodationId,
                        'image_path' => $path
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Accommodation created successfully',
                'data' => $accommodation->load(['images', 'amenities'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create accommodation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified accommodation.
     */
    public function show($id)
    {
        $accommodation = Accommodation::with([
            'owner',
            'images',
            'amenities',
            'deckImages',
            'reservations' => function($query) {
                $query->where('reservation_status', 'Approved')
                      ->whereDate('end_date', '>=', now())
                      ->with('tenant');
            }
        ])->find($id);

        if (!$accommodation) {
            return response()->json([
                'success' => false,
                'message' => 'Accommodation not found'
            ], 404);
        }

        // Increment view count if user is authenticated
        if (auth()->check()) {
            $accommodation->increment('views');
        }

        return response()->json([
            'success' => true,
            'data' => $accommodation
        ]);
    }

    /**
     * Update the specified accommodation.
     */
    public function update(Request $request, $id)
    {
        $accommodation = Accommodation::find($id);
        
        if (!$accommodation) {
            return response()->json([
                'success' => false,
                'message' => 'Accommodation not found'
            ], 404);
        }

        $user = Auth::user();
        
        if ($accommodation->user_id !== $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to update this accommodation'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'room_name' => 'sometimes|string|max:255',
            'monthly_rate' => 'sometimes|numeric|min:0',
            'max_occupants' => 'sometimes|integer|min:1',
            'gender_preference' => 'sometimes|in:Male,Female,Unisex',
            'total_beds' => 'sometimes|integer|min:1',
            'description' => 'nullable|string',
            'location' => 'sometimes|string',
            'status' => 'sometimes|in:Available,Occupied,Partially Occupied,Maintenance',
            'amenities' => 'nullable|array',
            'amenities.*' => 'exists:amenities,amenities_id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $accommodation->update(array_merge(
                $request->only([
                    'room_name', 'monthly_rate', 'max_occupants',
                    'gender_preference', 'total_beds', 'description',
                    'location', 'status'
                ]),
                ['date_updated' => Carbon::now()->toDateString()]
            ));

            // Sync amenities
            if ($request->has('amenities')) {
                $accommodation->amenities()->sync($request->amenities);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Accommodation updated successfully',
                'data' => $accommodation->fresh(['images', 'amenities'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update accommodation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified accommodation.
     */
    public function destroy($id)
    {
        $accommodation = Accommodation::find($id);
        
        if (!$accommodation) {
            return response()->json([
                'success' => false,
                'message' => 'Accommodation not found'
            ], 404);
        }

        $user = Auth::user();
        
        if ($accommodation->user_id !== $user->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this accommodation'
            ], 403);
        }

        // Check if there are active reservations
        $activeReservations = $accommodation->reservations()
            ->whereIn('reservation_status', ['Approved', 'Pending'])
            ->whereDate('end_date', '>=', now())
            ->exists();

        if ($activeReservations) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete accommodation with active reservations'
            ], 400);
        }

        $accommodation->delete();

        return response()->json([
            'success' => true,
            'message' => 'Accommodation deleted successfully'
        ]);
    }

    /**
     * Upload images for accommodation.
     */
    public function uploadImages(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array|min:1',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $accommodation = Accommodation::find($id);
        
        if (!$accommodation) {
            return response()->json([
                'success' => false,
                'message' => 'Accommodation not found'
            ], 404);
        }

        DB::beginTransaction();
        try {
            $uploadedImages = [];
            
            foreach ($request->file('images') as $image) {
                $imageId = 'img_' . Str::random(14);
                $path = $image->store('accommodations', 'public');
                
                $roomImage = RoomImage::create([
                    'room_images_id' => $imageId,
                    'accomodation_id' => $id,
                    'image_path' => $path
                ]);
                
                $uploadedImages[] = $roomImage;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Images uploaded successfully',
                'data' => $uploadedImages
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload images',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get amenities for accommodation.
     */
    public function getAmenities($id)
    {
        $accommodation = Accommodation::with('amenities')->find($id);
        
        if (!$accommodation) {
            return response()->json([
                'success' => false,
                'message' => 'Accommodation not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $accommodation->amenities
        ]);
    }

    /**
     * Get images for accommodation.
     */
    public function getImages($id)
    {
        $accommodation = Accommodation::with('images')->find($id);
        
        if (!$accommodation) {
            return response()->json([
                'success' => false,
                'message' => 'Accommodation not found'
            ], 404);
        }

        $images = $accommodation->images->map(function($image) {
            return [
                'id' => $image->room_images_id,
                'url' => $image->image_url,
                'thumbnail_url' => $image->thumbnail_url
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $images
        ]);
    }

    /**
     * Get deck images for accommodation.
     */
    public function getDeckImages($id)
    {
        $accommodation = Accommodation::with('deckImages')->find($id);
        
        if (!$accommodation) {
            return response()->json([
                'success' => false,
                'message' => 'Accommodation not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $accommodation->deckImages
        ]);
    }

    /**
     * Get owner's accommodations.
     */
    public function getOwnerAccommodations(Request $request)
    {
        $user = Auth::user();
        $status = $request->query('status');
        $perPage = $request->query('per_page', 10);
        
        $query = Accommodation::with(['images', 'amenities', 'reservations'])
            ->where('user_id', $user->user_id)
            ->orderBy('date_created', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        $accommodations = $query->paginate($perPage);

        // Add occupancy statistics
        $accommodations->getCollection()->transform(function($accommodation) {
            $accommodation->available_beds = $accommodation->available_beds;
            $accommodation->occupancy_percentage = $accommodation->occupancy_percentage;
            return $accommodation;
        });

        return response()->json([
            'success' => true,
            'data' => $accommodations
        ]);
    }

    /**
     * Search accommodations.
     */
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'query' => 'required|string|min:2',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $query = $request->query('query');
        
        $accommodations = Accommodation::with(['owner', 'images'])
            ->where(function($q) use ($query) {
                $q->where('room_name', 'like', '%' . $query . '%')
                  ->orWhere('description', 'like', '%' . $query . '%')
                  ->orWhere('location', 'like', '%' . $query . '%')
                  ->orWhere('accomodation_type', 'like', '%' . $query . '%')
                  ->orWhereHas('owner', function($ownerQuery) use ($query) {
                      $ownerQuery->where('house_name', 'like', '%' . $query . '%')
                                 ->orWhere('firstname', 'like', '%' . $query . '%')
                                 ->orWhere('lastname', 'like', '%' . $query . '%');
                  });
            })
            ->whereIn('status', ['Available', 'Partially Occupied'])
            ->inBulan()
            ->paginate(12);

        return response()->json([
            'success' => true,
            'data' => $accommodations,
            'search_query' => $query
        ]);
    }

    /**
     * Get municipal data for Bulan, Sorsogon.
     */
    public function getBulanMunicipalityData()
    {
        $data = [
            'municipality' => 'Bulan',
            'province' => 'Sorsogon',
            'zipcode' => '4706',
            'barangays' => [
                'Zone 1', 'Zone 2', 'Zone 3', 'Zone 4', 'Zone 5', 'Zone 6',
                'Zone 7', 'Zone 8', 'Zone 9', 'Zone 10',
                'San Juan', 'San Francisco', 'San Isidro',
                'Sta. Remedios', 'Sta. Teresita', 'Sta. Cruz',
                // Add more barangays as needed
            ],
            'accommodation_stats' => [
                'total' => Accommodation::inBulan()->count(),
                'available' => Accommodation::inBulan()->available()->count(),
                'boarding_houses' => Accommodation::inBulan()->type('Boarding House')->count(),
                'lodging_houses' => Accommodation::inBulan()->type('Lodging House')->count(),
            ],
            'contact_info' => [
                'municipal_hall' => '(056) 211-1234',
                'tourism_office' => '(056) 211-5678',
                'email' => 'tourism.bulan@sorsogon.gov.ph',
                'address' => 'Municipal Hall, Poblacion, Bulan, Sorsogon'
            ]
        ];

        return response()->json([
            'success' => true,
            'data' => $data
        ]);
    }

    /**
     * Get all accommodations (admin only).
     */
    public function getAllAccommodations(Request $request)
    {
        $user = Auth::user();
        
        if ($user->user_type !== 'Admin') {
            return response()->json([
                'success' => false,
                'message' => 'Admin access required'
            ], 403);
        }

        $perPage = $request->query('per_page', 20);
        $status = $request->query('status');
        $type = $request->query('type');
        
        $query = Accommodation::with(['owner', 'images'])
            ->orderBy('date_created', 'desc');

        if ($status) {
            $query->where('status', $status);
        }

        if ($type) {
            $query->where('accomodation_type', $type);
        }

        $accommodations = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $accommodations
        ]);
    }
}