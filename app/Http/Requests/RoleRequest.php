<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $role = $this->route('role');
        $roleId = (is_object($role) && property_exists($role, 'id')) ? $role->id : $role;

        return [
            'name' => 'required|string|max:255|unique:roles,name,' . $roleId,
            'description' => 'nullable|string|max:500',
        ];
    }
}