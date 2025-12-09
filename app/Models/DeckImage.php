<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeckImage extends Model
{
    use HasFactory;

    protected $table = 'deck_images';
    protected $primaryKey = 'deck_id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'deck_id',
        'images',
        'accomodation_id',
        'deck_number',
        'layers',
        'layer1',
        'layer2',
        'layer3',
        'layer4'
    ];

    protected $casts = [
        'layers' => 'integer',
        'deck_number' => 'integer',
    ];

    /**
     * Relationship with accommodation
     */
    public function accommodation()
    {
        return $this->belongsTo(Accommodation::class, 'accomodation_id', 'accomodation_id');
    }

    /**
     * Relationship with reservations
     */
    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'deck_id', 'deck_id');
    }

    /**
     * Scope for decks by accommodation
     */
    public function scopeByAccommodation($query, $accommodationId)
    {
        return $query->where('accomodation_id', $accommodationId);
    }

    /**
     * Scope for decks by deck number
     */
    public function scopeByDeckNumber($query, $deckNumber)
    {
        return $query->where('deck_number', $deckNumber);
    }

    /**
     * Scope for decks with available layers
     */
    public function scopeWithAvailableLayers($query)
    {
        return $query->where(function($q) {
            $q->where('layer1', 'Available')
              ->orWhere('layer1', 'Availble')
              ->orWhere('layer2', 'Available')
              ->orWhere('layer2', 'Availble')
              ->orWhere('layer3', 'Available')
              ->orWhere('layer3', 'Availble')
              ->orWhere('layer4', 'Available')
              ->orWhere('layer4', 'Availble');
        });
    }

    /**
     * Get available layers
     */
    public function getAvailableLayersAttribute(): array
    {
        $availableLayers = [];
        
        for ($i = 1; $i <= 4; $i++) {
            $layerField = "layer{$i}";
            if ($this->$layerField === 'Available' || $this->$layerField === 'Availble') {
                $availableLayers[] = [
                    'layer_number' => $i,
                    'layer_name' => "layer{$i}",
                    'layer_field' => $layerField,
                    'status' => 'Available',
                    'layer_display_name' => $this->getLayerDisplayName($i)
                ];
            }
        }
        
        return $availableLayers;
    }

    /**
     * Get all layers with status
     */
    public function getAllLayersAttribute(): array
    {
        $allLayers = [];
        
        for ($i = 1; $i <= 4; $i++) {
            $layerField = "layer{$i}";
            $status = $this->$layerField ?? 'Not Available';
            
            // Fix typo in status
            if ($status === 'Availble') {
                $status = 'Available';
            }
            
            $allLayers[] = [
                'layer_number' => $i,
                'layer_name' => "layer{$i}",
                'layer_field' => $layerField,
                'status' => $status,
                'is_available' => $status === 'Available',
                'is_occupied' => $status === 'Occupied',
                'layer_display_name' => $this->getLayerDisplayName($i)
            ];
        }
        
        return $allLayers;
    }

    /**
     * Get layer display name
     */
    private function getLayerDisplayName(int $layerNumber): string
    {
        $layerNames = [
            1 => 'Bottom Layer',
            2 => 'Lower Middle',
            3 => 'Upper Middle',
            4 => 'Top Layer'
        ];
        
        return $layerNames[$layerNumber] ?? "Layer {$layerNumber}";
    }

    /**
     * Check if specific layer is available
     */
    public function isLayerAvailable($layer): bool
    {
        $layerField = "layer{$layer}";
        return isset($this->$layerField) && 
               ($this->$layerField === 'Available' || $this->$layerField === 'Availble');
    }

    /**
     * Check if any layer is available
     */
    public function hasAvailableLayers(): bool
    {
        return count($this->available_layers) > 0;
    }

    /**
     * Get first available layer
     */
    public function getFirstAvailableLayerAttribute(): ?array
    {
        $availableLayers = $this->available_layers;
        return !empty($availableLayers) ? $availableLayers[0] : null;
    }

    /**
     * Get deck image URL
     */
    public function getImageUrlAttribute(): string
    {
        if (!$this->images) {
            return asset('images/default-deck.png');
        }
        
        if (filter_var($this->images, FILTER_VALIDATE_URL)) {
            return $this->images;
        }
        
        return asset('storage/decks/' . $this->images);
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute(): string
    {
        if (!$this->images) {
            return asset('images/default-deck-thumb.png');
        }
        
        $path = $this->images;
        $filename = pathinfo($path, PATHINFO_FILENAME);
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        
        $thumbnail = $filename . '_thumb.' . $extension;
        
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path; // Return original if it's a URL
        }
        
        return asset('storage/decks/thumbnails/' . $thumbnail);
    }

    /**
     * Get deck type based on layers
     */
    public function getDeckTypeAttribute(): string
    {
        switch ($this->layers) {
            case 1:
                return 'Single Deck';
            case 2:
                return 'Double Deck';
            case 3:
                return 'Triple Deck';
            case 4:
                return 'Quadruple Deck';
            default:
                return 'Custom Deck';
        }
    }

    /**
     * Get deck capacity (max occupants based on layers)
     */
    public function getCapacityAttribute(): int
    {
        // Count layers that can be occupied
        $occupiedLayers = 0;
        for ($i = 1; $i <= $this->layers; $i++) {
            $layerField = "layer{$i}";
            if ($this->$layerField !== 'Not Available') {
                $occupiedLayers++;
            }
        }
        return $occupiedLayers;
    }

    /**
     * Get current occupancy count
     */
    public function getCurrentOccupancyAttribute(): int
    {
        return Reservation::where('deck_id', $this->deck_id)
            ->where('reservation_status', 'Approved')
            ->whereDate('end_date', '>=', now())
            ->count();
    }

    /**
     * Get occupancy percentage
     */
    public function getOccupancyPercentageAttribute(): float
    {
        if ($this->capacity === 0) {
            return 0;
        }
        
        return ($this->current_occupancy / $this->capacity) * 100;
    }

    /**
     * Get reservations for this deck
     */
    public function getCurrentReservationsAttribute()
    {
        return Reservation::with(['tenant', 'accommodation'])
            ->where('deck_id', $this->deck_id)
            ->where('reservation_status', 'Approved')
            ->whereDate('end_date', '>=', now())
            ->get()
            ->map(function($reservation) {
                return [
                    'reservation_id' => $reservation->reservation_id,
                    'tenant_name' => $reservation->tenant ? $reservation->tenant->full_name : 'Unknown',
                    'start_date' => $reservation->start_date,
                    'end_date' => $reservation->end_date,
                    'layer' => $reservation->layer,
                    'bed_desk' => $reservation->bed_desk,
                    'days_remaining' => $reservation->calculateTotalDays() - 
                        \Carbon\Carbon::parse($reservation->start_date)->diffInDays(now())
                ];
            });
    }

    /**
     * Update layer status
     */
    public function updateLayerStatus($layer, $status): bool
    {
        $validStatuses = ['Available', 'Occupied', 'Not Available', 'Maintenance', 'Reserved'];
        
        if (!in_array($status, $validStatuses)) {
            return false;
        }
        
        $layerField = "layer{$layer}";
        
        if ($layer > $this->layers) {
            return false;
        }
        
        $this->$layerField = $status;
        return $this->save();
    }

    /**
     * Reserve a specific layer
     */
    public function reserveLayer($layer): bool
    {
        if (!$this->isLayerAvailable($layer)) {
            return false;
        }
        
        return $this->updateLayerStatus($layer, 'Reserved');
    }

    /**
     * Mark layer as occupied
     */
    public function occupyLayer($layer): bool
    {
        return $this->updateLayerStatus($layer, 'Occupied');
    }

    /**
     * Mark layer as available
     */
    public function freeLayer($layer): bool
    {
        return $this->updateLayerStatus($layer, 'Available');
    }

    /**
     * Get layer status color
     */
    public function getLayerStatusColor($status): string
    {
        $colors = [
            'Available' => 'success',
            'Availble' => 'success', // Handle typo
            'Occupied' => 'danger',
            'Not Available' => 'secondary',
            'Maintenance' => 'warning',
            'Reserved' => 'info'
        ];
        
        return $colors[$status] ?? 'secondary';
    }

    /**
     * Get deck summary for display
     */
    public function getDeckSummaryAttribute(): array
    {
        return [
            'deck_id' => $this->deck_id,
            'deck_number' => $this->deck_number,
            'deck_type' => $this->deck_type,
            'total_layers' => $this->layers,
            'capacity' => $this->capacity,
            'current_occupancy' => $this->current_occupancy,
            'available_layers' => count($this->available_layers),
            'occupancy_percentage' => $this->occupancy_percentage,
            'image_url' => $this->image_url,
            'thumbnail_url' => $this->thumbnail_url,
            'layers' => $this->all_layers,
            'reservations' => $this->current_reservations,
            'accommodation' => $this->accommodation ? [
                'id' => $this->accommodation->accomodation_id,
                'room_name' => $this->accommodation->room_name,
                'type' => $this->accommodation->accomodation_type
            ] : null
        ];
    }

    /**
     * Get deck pricing (if different per layer)
     */
    public function getLayerPricingAttribute(): array
    {
        $accommodation = $this->accommodation;
        $monthlyRate = $accommodation ? $accommodation->monthly_rate : 0;
        
        $layerPricing = [];
        for ($i = 1; $i <= $this->layers; $i++) {
            // Adjust pricing based on layer position (top might be cheaper, bottom more expensive)
            $layerMultiplier = 1.0;
            
            switch ($i) {
                case 1: // Bottom layer - most expensive
                    $layerMultiplier = 1.2;
                    break;
                case 2: // Lower middle
                    $layerMultiplier = 1.1;
                    break;
                case 3: // Upper middle
                    $layerMultiplier = 0.9;
                    break;
                case 4: // Top layer - least expensive
                    $layerMultiplier = 0.8;
                    break;
            }
            
            $layerPrice = $monthlyRate * $layerMultiplier;
            
            $layerPricing[] = [
                'layer_number' => $i,
                'layer_name' => "Layer {$i}",
                'monthly_rate' => round($layerPrice, 2),
                'daily_rate' => round($layerPrice / 30, 2),
                'display_name' => $this->getLayerDisplayName($i),
                'is_available' => $this->isLayerAvailable($i)
            ];
        }
        
        return $layerPricing;
    }

    /**
     * Check if deck is fully occupied
     */
    public function isFullyOccupied(): bool
    {
        return $this->current_occupancy >= $this->capacity;
    }

    /**
     * Get next available date for this deck
     */
    public function getNextAvailableDateAttribute(): ?string
    {
        $lastReservation = Reservation::where('deck_id', $this->deck_id)
            ->where('reservation_status', 'Approved')
            ->whereDate('end_date', '>=', now())
            ->orderBy('end_date', 'desc')
            ->first();
        
        if ($lastReservation) {
            return \Carbon\Carbon::parse($lastReservation->end_date)->addDay()->format('Y-m-d');
        }
        
        return now()->format('Y-m-d');
    }

    /**
     * Create deck for accommodation
     */
    public static function createForAccommodation($accommodationId, $deckNumber, $layers = 2, $imagePath = null): ?DeckImage
    {
        $deckId = 'deck_' . uniqid();
        
        // Set default layer statuses
        $layerData = [];
        for ($i = 1; $i <= 4; $i++) {
            $layerData["layer{$i}"] = $i <= $layers ? 'Available' : 'Not Available';
        }
        
        return self::create(array_merge([
            'deck_id' => $deckId,
            'accomodation_id' => $accommodationId,
            'deck_number' => $deckNumber,
            'layers' => $layers,
            'images' => $imagePath,
        ], $layerData));
    }
}