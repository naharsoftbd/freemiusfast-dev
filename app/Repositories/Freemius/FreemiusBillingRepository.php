<?php

namespace App\Repositories\Freemius;

use App\Models\Freemius\FreemiusBilling;
use App\Interfaces\Freemius\FreemiusBillingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class FreemiusBillingRepository implements FreemiusBillingRepositoryInterface
{
    public function all(): Collection
    {
        return FreemiusBilling::all();
    }

    public function find(int $id): ?FreemiusBilling
    {
        return FreemiusBilling::find($id);
    }

    public function create(array $data): FreemiusBilling
    {
        return FreemiusBilling::create($data);
    }

    public function update(int $id, array $data): bool
    {
        return FreemiusBilling::where('id', $id)->update($data);
    }

    public function delete(int $id): bool
    {
        return FreemiusBilling::destroy($id);
    }
}
