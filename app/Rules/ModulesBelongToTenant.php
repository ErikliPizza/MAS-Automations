<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;

class ModulesBelongToTenant implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $tenantModules = Auth::user()->tenant->modules->pluck('id')->toArray();

        if (array_diff($value, $tenantModules)) {
            $fail('One or more of the selected modules are not associated with the current tenant.');
        }
    }
}
