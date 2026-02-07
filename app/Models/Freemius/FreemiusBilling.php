<?php

namespace App\Models\Freemius;

use Illuminate\Database\Eloquent\Model;

class FreemiusBilling extends Model
{
    protected $fillable = [
        'fs_user_id',
        'business_name',
        'first',
        'last',
        'email',
        'phone',
        'website',
        'tax_id',
        'address_street',
        'address_apt',
        'address_city',
        'address_state',
        'address_zip',
        'address_country',
        'address_country_code',
        'fs_created_at',
        'fs_updated_at',
    ];
}
