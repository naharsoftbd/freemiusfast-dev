<?php

namespace App\Services\Freemius;

use App\Interfaces\Freemius\FreemiusBillingRepositoryInterface;

class FreemiusBillingService
{

    protected $freemiusRepo;
    /**
     * Create a new class instance.
     */
    public function __construct(FreemiusBillingRepositoryInterface $freemiusRepo)
    {
        $this->freemiusRepo = $freemiusRepo;
    }

    public function updateByFsUserId(array $data, $fs_user_id)
    {
        return $this->freemiusRepo->updateByFsUserId($data, $fs_user_id);
    }
}
