<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Service\Admin\ExternalAccessService;
use App\Http\Requests\Admin\ExternalAccessGetRequest;
use App\Http\Requests\Admin\ExternalAccessStoreRequest;

class ExternalAccessController extends Controller
{
    protected $externalAccessService;

    public function __construct(ExternalAccessService $externalAccessService)
    {
        $this->externalAccessService = $externalAccessService;
    }

    public function get(ExternalAccessGetRequest $request): JsonResponse
    {
        // Fetch data using the service
        $data = $this->externalAccessService->get($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Data fetched successfully',
            'data' => $data
        ], 200);
    }

    public function store(ExternalAccessStoreRequest $request): JsonResponse
    {
        // Store data using the service
        $externalAccess = $this->externalAccessService->store($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'External access created successfully',
            'external_access' => $externalAccess
        ], 201);
    }

    public function update(string $id, ExternalAccessStoreRequest $request): JsonResponse
    {
        // Update data using the service
        $externalAccess = $this->externalAccessService->update($id, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'External access updated successfully',
            'external_access' => $externalAccess
        ], 201);
    }
}
