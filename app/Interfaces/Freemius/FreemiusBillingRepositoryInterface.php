<?php

namespace App\Interfaces\Freemius;

interface FreemiusBillingRepositoryInterface
{
    public function updateByFsUserId(array $data, $fs_user_id);
}
