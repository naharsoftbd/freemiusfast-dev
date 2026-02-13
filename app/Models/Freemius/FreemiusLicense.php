<?php

namespace App\Models\Freemius;

use Illuminate\Database\Eloquent\Model;

class FreemiusLicense extends Model
{
    protected $fillable = [
        'freemius_id', 'fs_user_id', 'product_id', 'user_id', 'plan_id', 'pricing_id', 'quota',
        'activated', 'activated_local', 'secret_key', 'is_free_localhost',
        'is_block_features', 'is_cancelled', 'is_whitelabeled', 'environment', 'source',
        'expiration', 'freemius_created_at', 'freemius_updated_at',
    ];
    
    protected $casts = [
            'products' => 'array',
            'is_free_localhost' => 'boolean',
            'is_block_features' => 'boolean',
            'is_cancelled' => 'boolean',
            'is_whitelabeled' => 'boolean',
            'expiration' => 'datetime',
            'secret_key' => 'encrypted',
        ];
}
