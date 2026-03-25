<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseBatch extends Model
{
    const STATUS_DRAFT = 'draft';
    const STATUS_OPEN = 'open';
    const STATUS_CLOSED = 'closed';
    const STATUS_COMPLETED = 'completed';

    public static $statuses = ['draft', 'open', 'closed', 'completed'];

    protected $table = 'course_batches';

    public $timestamps = true;

    protected $dateFormat = 'U';

    protected $fillable = [
        'webinar_id',
        'name',
        'code',
        'start_date',
        'end_date',
        'capacity',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'start_date' => 'integer',
        'end_date' => 'integer',
        'capacity' => 'integer',
        'sort_order' => 'integer',
    ];

    public function webinar()
    {
        return $this->belongsTo(Webinar::class, 'webinar_id', 'id');
    }

    public function sales()
    {
        return $this->hasMany(Sale::class, 'batch_id', 'id')
            ->whereNull('refund_at')
            ->where('type', Sale::$webinar);
    }

    public function carts()
    {
        return $this->hasMany(Cart::class, 'batch_id', 'id');
    }

    /**
     * Whether this batch is open for enrollment.
     */
    public function isOpen(): bool
    {
        if ($this->status !== self::STATUS_OPEN) {
            return false;
        }
        if ($this->isFull()) {
            return false;
        }
        $now = time();
        if ($this->start_date !== null && $now < $this->start_date) {
            return true;
        }
        if ($this->end_date !== null && $now > $this->end_date) {
            return false;
        }
        return true;
    }

    /**
     * Whether the batch has reached capacity.
     */
    public function isFull(): bool
    {
        if ($this->capacity === null) {
            return false;
        }
        $enrolled = Sale::where('batch_id', $this->id)
            ->whereNull('refund_at')
            ->where('type', Sale::$webinar)
            ->count();
        return $enrolled >= $this->capacity;
    }

    /**
     * Number of enrolled students (non-refunded).
     */
    public function getEnrolledCountAttribute(): int
    {
        return Sale::where('batch_id', $this->id)
            ->whereNull('refund_at')
            ->where('type', Sale::$webinar)
            ->count();
    }

    public function hasStarted(): bool
    {
        return $this->start_date !== null && time() >= $this->start_date;
    }

    public function hasEnded(): bool
    {
        return $this->end_date !== null && time() > $this->end_date;
    }

    public function scopeOpen($query)
    {
        return $query->where('status', self::STATUS_OPEN);
    }

    public function scopeForWebinar($query, $webinarId)
    {
        return $query->where('webinar_id', $webinarId);
    }
}
