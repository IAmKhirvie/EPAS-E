<?php

namespace App\Http\Requests;

use App\Constants\Roles;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name'    => 'required|string|max:255',
            'middle_name'   => 'nullable|string|max:255',
            'last_name'     => 'required|string|max:255',
            'ext_name'      => 'nullable|string|max:10',
            'email'         => 'required|email|unique:users,email',
            'role'          => 'required|string|in:' . implode(',', Roles::all()),
            'department_id' => 'required|exists:departments,id',
            'stat'          => 'required|boolean',
            'password'      => 'required|string|min:6|confirmed',
            'section'       => 'nullable|string|max:255',
            'room_number'   => 'nullable|string|max:255',
        ];
    }
}
