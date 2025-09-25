<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;

class WarehousesController extends Controller
{
    public function index()
    {
        $warehouses = Warehouse::with(['createdBy', 'updatedBy'])->get();
        return WarehouseResource::collection($warehouses);
    }
}