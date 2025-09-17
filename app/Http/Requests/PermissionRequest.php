<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $permission = $this->route('permission');
        $permissionId = is_object($permission) && property_exists($permission, 'id') ? $permission->id : $permission;

        return [
            'name' => 'required|string|max:255|unique:permissions,name,' . $permissionId,
            'description' => 'nullable|string|max:500',
        ];
    }
}