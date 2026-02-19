<?php

namespace App\Interfaces\Freemius;

interface FreemiusBillingRepositoryInterface
{
    public function getUserBilling($fs_user_id);
    public function updateByFsUserId(array $data, $fs_user_id);
}
