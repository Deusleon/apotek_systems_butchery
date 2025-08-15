<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImportHistory extends Model
{
    protected $table = 'import_history';

    protected $fillable = [
        'file_name',
        'store_id',
        'price_category_id',
        'supplier_id',
        'total_records',
        'successful_records',
        'failed_records',
        'status',
        'error_log',
        'created_by',
        'started_at',
        'completed_at',
        'processing_time',
        'progress',
        'metadata',
        'processed_rows',
        'final_summary'
    ];

    protected $casts = [
        'metadata' => 'array',
        'processed_rows' => 'array',
        'final_summary' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_COMPLETED = 'completed';
    const STATUS_COMPLETED_WITH_ERRORS = 'completed_with_errors';
    const STATUS_FAILED = 'failed';

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function priceCategory()
    {
        return $this->belongsTo(PriceCategory::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Helper methods
    public function getSuccessRateAttribute()
    {
        if ($this->total_records > 0) {
            return ($this->successful_records / $this->total_records) * 100;
        }
        return 0;
    }

    public function getDurationAttribute()
    {
        if ($this->started_at && $this->completed_at) {
            return $this->started_at->diffInSeconds($this->completed_at);
        }
        return $this->processing_time ?? 0;
    }

    public function isCompleted()
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_COMPLETED_WITH_ERRORS]);
    }

    public function isFailed()
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function isInProgress()
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => 'badge-warning',
            self::STATUS_PROCESSING => 'badge-info',
            self::STATUS_COMPLETED => 'badge-success',
            self::STATUS_COMPLETED_WITH_ERRORS => 'badge-warning',
            self::STATUS_FAILED => 'badge-danger'
        ];

        return $badges[$this->status] ?? 'badge-secondary';
    }

    public function getFormattedErrorLogAttribute()
    {
        if (empty($this->error_log)) {
            return [];
        }
        return explode("\n", $this->error_log);
    }
} 