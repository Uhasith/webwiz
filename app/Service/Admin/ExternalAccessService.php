<?php

namespace App\Service\Admin;

use App\Models\ExternalAccess;
use App\Repository\Admin\ExternalAccessRepo;

class ExternalAccessService
{
    protected $externalAccessRepo;

    public function __construct(ExternalAccessRepo $repository)
    {
        $this->externalAccessRepo = $repository;
    }

    public function get(array $data): array
    {
        return $this->externalAccessRepo->getData($data);
    }

    public function store(array $data): ExternalAccess
    {
        return $this->externalAccessRepo->create($data);
    }

    public function update(string $id, array $data): ExternalAccess
    {
        return $this->externalAccessRepo->update($id, $data);
    }
}
