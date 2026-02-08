<?php

namespace App\Interfaces\Freemius;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Freemius\FreemiusBilling;

interface FreemiusBillingRepositoryInterface
{
    public function updateByFsUserId(array $data, $fs_user_id);
}
