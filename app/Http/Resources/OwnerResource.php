<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OwnerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'mobile_no' => $this->mobile_no,
            'company_name' => $this->company_name,
            'employee_id' => $this->employee_id,
            'verification' => $this->verification,
            // Accessors for URLs
            'electricity_bill_url' => $this->electricity_bill_url,
            'pan_card_url' => $this->pan_card_url,
            'agreement_url' => $this->agreement_url,
            'image_url' => $this->image_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_owner' => true,
        ];
    }
}
