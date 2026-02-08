<?php

namespace App\Interfaces\Freemius;

use Illuminate\Database\Eloquent\Collection;
use App\Models\Freemius\FreemiusBilling;

interface FreemiusBillingRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?FreemiusBilling;

    public function create(array $data): FreemiusBilling;

    public function update(int $id, array $data): bool;

    public function delete(int $id): bool;
}
