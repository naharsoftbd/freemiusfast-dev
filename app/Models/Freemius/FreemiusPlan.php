<?php

namespace App\Models\Freemius;

use Illuminate\Database\Eloquent\Model;

class FreemiusPlan extends Model
{
    protected $fillable = [
        'plan_id',
        'plugin_id',
        'name',
        'title',
        'description',
        'is_free_localhost',
        'is_block_features',
        'is_block_features_monthly',
        'license_type',
        'is_https_support',
        'trial_period',
        'is_require_subscription',
        'support_kb',
        'support_forum',
        'support_email',
        'support_phone',
        'support_skype',
        'is_success_manager',
        'is_featured',
        'is_hidden',
        'freemius_created_at',
        'freemius_updated_at',
    ];

    protected $casts = [
        'is_free_localhost' => 'boolean',
        'is_block_features' => 'boolean',
        'is_block_features_monthly' => 'boolean',
        'is_https_support' => 'boolean',
        'is_require_subscription' => 'boolean',
        'is_success_manager' => 'boolean',
        'is_featured' => 'boolean',
        'is_hidden' => 'boolean',
        'license_type' => 'integer',
        'trial_period' => 'integer',
        'freemius_created_at' => 'datetime',
        'freemius_updated_at' => 'datetime',
    ];

    public function pricings()
    {
        return $this->hasMany(FreemiusPricing::class, 'plan_id', 'plan_id');
    }

    public function features()
    {
        return $this->belongsToMany(
                FreemiusFeature::class,
                'freemius_plan_feature',   // pivot table
                'plan_id',                 // foreign key on pivot for this model
                'feature_id'               // foreign key on pivot for related model
                )->withPivot(['value', 'is_featured'])
                ->withTimestamps();
    }
}
