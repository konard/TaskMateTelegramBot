<?php

declare(strict_types=1);

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
        $data = [
            'id'           => $this->id,
            'login'        => $this->login,
            'full_name'    => $this->full_name,
            'role'         => $this->role,
            'telegram_id'  => $this->telegram_id,
            'phone_number' => $this->phone,
            'dealership_id' => $this->dealership_id,
            // 'status'       => $this->status,
        ];

        // Include dealership data if loaded
        if ($this->relationLoaded('dealership') && $this->dealership) {
            $data['dealership'] = [
                'id' => $this->dealership->id,
                'name' => $this->dealership->name,
                'address' => $this->dealership->address,
                'phone' => $this->dealership->phone,
                'is_active' => $this->dealership->is_active,
            ];
        }

        return $data;
    }
}
