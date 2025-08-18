<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
       return [
            'id'         => $this->id,
            'name'       => $this->name,
            'email'      => $this->email,

            'branch' => [
                'id' => $this->branch->id ?? null,
                'name' => $this->branch->name ?? null,
            ],

            'counter' => [
                'id' => $this->counter->id ?? null,
                'name' => $this->counter->name ?? null,
            ],

            'status' => [
                'id' => $this->status->id ?? null,
                'name' => $this->status->name ?? null,
            ],

            'created_by' => [
                'id' => $this->createdBy->id ?? null,
                'name' => $this->createdBy->name ?? null,
            ],

            'updated_by' => [
                'id' => $this->updatedBy->id ?? null,
                'name' => $this->updatedBy->name ?? null,
            ],
        ];
    }
}
