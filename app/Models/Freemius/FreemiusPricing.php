<?php

namespace App\Models\Freemius;

use Illuminate\Database\Eloquent\Model;

class FreemiusPricing extends Model
{
    protected $fillable = [
        'pricing_id',
        'plan_id',
        'licenses',
        'monthly_price',
        'annual_price',
        'lifetime_price',
        'currency',
        'is_whitelabeled',
        'is_hidden',
        'freemius_created_at',
        'freemius_updated_at',
    ];

    protected $casts = [
        'licenses' => 'integer',
        'monthly_price' => 'decimal:2',
        'annual_price' => 'decimal:2',
        'lifetime_price' => 'decimal:2',
        'is_whitelabeled' => 'boolean',
        'is_hidden' => 'boolean',
        'freemius_created_at' => 'datetime',
        'freemius_updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function plan()
    {
        return $this->belongsTo(FreemiusPlan::class, 'plan_id', 'plan_id');
    }
}
