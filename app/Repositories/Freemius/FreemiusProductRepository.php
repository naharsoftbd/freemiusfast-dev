<?php

namespace App\Repositories\Freemius;

use App\Interfaces\Freemius\FreemiusProductRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FreemiusProductRepository implements FreemiusProductRepositoryInterface
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

}
