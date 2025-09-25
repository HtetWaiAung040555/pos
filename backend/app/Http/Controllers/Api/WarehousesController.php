<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\WarehouseResource;
use App\Models\Warehouse;
use Illuminate\Http\Request;

class WarehousesController extends Controller
{
    public function index()
    {
        $branches = Warehouse::with(['warehouse','status','createdBy', 'updatedBy'])->get();
        return WarehouseResource::collection($branches);
    }
}