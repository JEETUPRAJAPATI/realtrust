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
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'mobile_no' => $this->mobile_no,
            'verification' => $this->verification,
            // Accessors for URLs
            'agreement_url' => $this->agreement_url,
            'image_url' => $this->image_url,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'is_user' => true,
                'documents' => $this->userDocuments->map(function ($doc) {
            return [
                'id' => $doc->id,
                'company_name' => $doc->company_name,
                'employee_id' => $doc->employee_id,
                'aadhaar_card_url' => $doc->aadhaar_card_url, // Accessor is automatically used
                'pan_card_url' => $doc->pan_card_url,         // Accessor is automatically used
            ];
            })->toArray(),

        ];
    }
}
