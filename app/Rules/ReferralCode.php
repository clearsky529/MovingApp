<?php

namespace App\Rules;
use App\Companies;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ReferralCode implements ValidationRule
{
    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value != null) {
            $company = Companies::where('referral_code',$value)->exists();
            if (empty($company)) {
                $fail('Invalid referral code.');
            }
        }
    }
}
