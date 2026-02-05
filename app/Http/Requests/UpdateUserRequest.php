<?php

namespace App\Http\Requests;

use App\Constants\Roles;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $userId = is_object($user) ? $user->id : $user;

        $roleRule = auth()->user()?->role === Roles::ADMIN
            ? 'required|string|in:' . implode(',', Roles::all())
            : 'prohibited';

        return [
            'student_id'     => 'required|string|max:25|unique:users,student_id,' . $userId,
            'first_name'     => 'required|string|max:255',
            'middle_name'    => 'nullable|string|max:255',
            'last_name'      => 'required|string|max:255',
            'ext_name'       => 'nullable|string|max:10',
            'email'          => 'required|email|unique:users,email,' . $userId,
            'role'           => $roleRule,
            'department_id'  => 'nullable|exists:departments,id',
            'stat'           => 'required|boolean',
            'password'       => 'nullable|string|min:6|confirmed',
            'section'        => 'nullable|string|max:255',
            'custom_section' => 'nullable|string|max:255',
            'room_number'    => 'nullable|string|max:255',
        ];
    }
}
