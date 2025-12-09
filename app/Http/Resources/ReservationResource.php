<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->reservation_id,
            'tenant' => [
                'id' => $this->tenant->user_id,
                'name' => $this->tenant->firstname . ' ' . $this->tenant->lastname,
                'email' => $this->tenant->email,
                'mobile_number' => $this->tenant->mobile_number,
            ],
            'owner' => [
                'id' => $this->owner->user_id,
                'name' => $this->owner->firstname . ' ' . $this->owner->lastname,
                'house_name' => $this->owner->house_name,
            ],
            'accommodation' => [
                'id' => $this->accommodation->accomodation_id,
                'room_name' => $this->accommodation->room_name,
                'type' => $this->accommodation->accomodation_type,
                'monthly_rate' => $this->accommodation->monthly_rate,
                'location' => $this->accommodation->location,
            ],
            'status' => $this->reservation_status,
            'notification_status' => $this->notification_status,
            'dates' => [
                'application' => $this->date_application,
                'application_time' => $this->time_application,
                'start' => $this->start_date,
                'end' => $this->end_date,
                'expiration' => $this->date_expiration,
                'expiration_time' => $this->time_expiration,
            ],
            'bed_details' => [
                'bed_desk' => $this->bed_desk,
                'layer' => $this->layer,
                'deck_id' => $this->deck_id,
            ],
            'duration' => $this->time_duration,
            'total_days' => $this->calculateTotalDays(),
            'is_active' => $this->isActive(),
            'is_expired' => $this->isExpired(),
            'status_user' => $this->status_user,
            'hide_status' => $this->hide_status,
            'created_at' => $this->date_application,
            'updated_at' => $this->date_updated ?? $this->date_application,
        ];
    }
}