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
        return [
            'id'           => $this->id,
            'login'        => $this->login,
            'role'         => $this->role,
            'full_name'    => $this->full_name,
            'phone_number' => $this->phone_number,
            'company'      => new CompanyResource($this->whenLoaded('company')),
            'department'   => new DepartmentResource($this->whenLoaded('department')),
            'post'         => new PostResource($this->whenLoaded('post')),
        ];
    }
}
