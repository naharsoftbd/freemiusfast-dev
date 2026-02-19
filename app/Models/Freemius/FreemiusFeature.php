<?php

namespace App\Models\Freemius;

use Illuminate\Database\Eloquent\Model;

class FreemiusFeature extends Model
{
    protected $fillable = [
        'feature_id',
        'title',
        'description'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function plans()
    {
        return $this->belongsToMany(
                FreemiusPlan::class,
                'freemius_plan_feature',
                'feature_id',
                'plan_id'
            )->withPivot(['value', 'is_featured'])
            ->withTimestamps();
    }
}
