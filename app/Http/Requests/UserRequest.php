<?php

namespace App\Http\Requests;

use App\Traits\ModuleAssigner;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use App\Rules\ModulesBelongToTenant;

class UserRequest extends FormRequest
{
    use ModuleAssigner; // assign all tenant modules to the user if the updated-created user role is admin or root

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(Request $request): array
    {
        $user = $this->route('user');

        // Set all available modules in this organization if the new user role is admin. Not crucially necessary but a good practice.
        if ($request->isMethod('put') || $request->isMethod('patch')) {
            $request = $this->setModules($request, $user);
        } else {
            $request = $this->setModules($request, Auth::user());
        }

        $rules = [
            'name' => 'required|string|min:2|max:64',
            'surname' => 'required|string|min:2|max:64',
            'password' => ['required', Rules\Password::defaults()],
            'phone' => 'nullable|regex:/^\d{3,15}$/',

            'role' => 'required|string|in:admin,additional',

            'title' => 'nullable|string|min:2|max:64',
            'birthday' => 'nullable|date_format:Y-m-d',
            'id_number' => 'nullable|numeric|digits_between:1,20',
            'bank_account' => 'nullable|string|min:6|max:255',

            'start_date_of_work' => 'nullable|date',
            'end_date_of_work' => 'nullable|date|after_or_equal:start_date_of_work',
            'reason_of_leaving' => 'nullable|string|max:500',
            'salary' => 'nullable|numeric|between:0,99999999',

            'modules' => ['required', 'array', 'min:1', new ModulesBelongToTenant()],
            'modules.*' => ['required', 'exists:modules,id'],
        ];

        // Check if the request is for updating a record
        if ($request->isMethod('put') || $request->isMethod('patch')) {
            // Apply update validation rules for email
            $rules['email'] = [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ];
            $rules['status'] = 'required|string|in:active,inactive';
        } else {
            // Apply store validation rules for email
            $rules['email'] = 'required|string|email|max:255|unique:users';
            $request->merge(['status' => 'active']);
        }
        return $rules;
    }
}
