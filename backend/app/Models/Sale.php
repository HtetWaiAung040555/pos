<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    protected $table = 'sales';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $fillable = [
        'invoice_no',
        'customer_id',
        'total_amount',
        'paid_amount',
        'due_amount',
        'payment_method',
        'status_id',
        'sale_date',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'sale_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($sale) {
            // Generate date-based prefix like "081125"
            $dateCode = Carbon::now()->format('dmy');

            // Find last ID starting with today's prefix
            $lastSale = self::where('id', 'like', "S-{$dateCode}%")
                ->orderBy('id', 'desc')
                ->first();

            // Determine next counter
            if ($lastSale) {
                $lastNumber = intval(substr($lastSale->id, -5));
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            // Build new ID (e.g., S-08112500001)
            $sale->id = 'S-' . $dateCode . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function status() {
        return $this->belongsTo(Status::class);
    }

    public function createdBy() { 
        return $this->belongsTo(User::class, 'created_by'); 
    }

    public function updatedBy() { 
        return $this->belongsTo(User::class, 'updated_by'); 
    }
}
