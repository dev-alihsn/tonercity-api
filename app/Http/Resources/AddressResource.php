<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'label' => $this->label,
            'city' => $this->city,
            'address_line' => $this->address_line,
            'postal_code' => $this->postal_code,
            'phone' => $this->phone,
            'is_default' => $this->is_default,
        ];
    }
}
