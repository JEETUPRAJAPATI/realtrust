<?php

namespace App\Rules;

use Closure;
use App\Models\ConformTiming;
use Illuminate\Contracts\Validation\Rule;

class ConformTimingRule implements Rule
{
    protected $message;

    public function passes($attribute, $value)
    {;
        // Assuming $value is the property ID and field manager ID is passed from the request
        $conformTiming = ConformTiming::where('field_manager_id', request()->field_manager_id)
            ->where('property_id', request()->properties)
            ->where('conform_timing', 1)
            ->first();
        // dd($conformTiming);
        if (!$conformTiming) {
            $this->message = 'You need to set the conform timing for this property.';
            return false; // Validation fails
        }

        return true; // Validation passes
    }

    public function message()
    {
        return $this->message;
    }
}
