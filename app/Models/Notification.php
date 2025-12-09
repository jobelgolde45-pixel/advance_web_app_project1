<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $table = 'notifications';
    protected $primaryKey = 'notification_id';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'notification_id',
        'notification_message',
        'receiver',
        'sender',
        'seen',
        'date_sent'
    ];

    protected $casts = [
        'date_sent' => 'datetime',
    ];

    /**
     * Relationship with receiver (user)
     */
    public function receiverUser()
    {
        return $this->belongsTo(Account::class, 'receiver', 'user_id');
    }

    /**
     * Relationship with sender (user)
     */
    public function senderUser()
    {
        return $this->belongsTo(Account::class, 'sender', 'user_id');
    }

    /**
     * Scope for unread notifications
     */
    public function scopeUnread($query)
    {
        return $query->where('seen', 'unsee');
    }

    /**
     * Scope for user's notifications
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('receiver', $userId);
    }

    /**
     * Scope for recent notifications (last 30 days)
     */
    public function scopeRecent($query)
    {
        return $query->where('date_sent', '>=', now()->subDays(30));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead()
    {
        $this->seen = 'seen';
        $this->save();
    }

    /**
     * Check if notification is unread
     */
    public function isUnread(): bool
    {
        return $this->seen === 'unsee';
    }

    /**
     * Get notification type based on message content
     */
    public function getTypeAttribute(): string
    {
        $message = strtolower($this->notification_message);
        
        if (str_contains($message, 'reservation') && str_contains($message, 'approved')) {
            return 'reservation_approved';
        } elseif (str_contains($message, 'reservation') && str_contains($message, 'denied')) {
            return 'reservation_denied';
        } elseif (str_contains($message, 'reservation request')) {
            return 'new_reservation';
        } elseif (str_contains($message, 'cancelled')) {
            return 'reservation_cancelled';
        } elseif (str_contains($message, 'extended')) {
            return 'reservation_extended';
        } else {
            return 'general';
        }
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date_sent->format('M d, Y h:i A');
    }

    /**
     * Get short message preview
     */
    public function getPreviewAttribute(): string
    {
        return strlen($this->notification_message) > 100 
            ? substr($this->notification_message, 0, 100) . '...' 
            : $this->notification_message;
    }
}