<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Bill extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'bill_number',
        'shop_id',
        'user_id',
        'customer_id',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount',
        'tax',
        'total',
        'paid_amount',
        'due_amount',
        'payment_status',
        'payment_method',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'discount_value' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'due_amount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Bill $bill): void {
            if (empty($bill->uuid)) {
                $bill->uuid = (string) Str::uuid();
            }
        });
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BillItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeByShop($query, int $shopId)
    {
        return $query->where('shop_id', $shopId);
    }

    public function scopeByDateRange($query, string $from, ?string $to = null)
    {
        $query->whereDate('created_at', '>=', $from);

        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }

        return $query;
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('bill_number', 'like', "%{$term}%")
                ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', "%{$term}%")
                    ->orWhere('phone', 'like', "%{$term}%")
                );
        });
    }

    public function isPaid(): bool
    {
        return $this->payment_status === PaymentStatus::Paid->value;
    }

    public function isCredit(): bool
    {
        return $this->due_amount > 0;
    }
}
