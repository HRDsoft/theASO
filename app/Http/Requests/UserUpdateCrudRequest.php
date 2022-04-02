<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateCrudRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // return false;
        return backpack_auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // return [
        //     //
        // ];
        $id = $this->get('id') ?? request()->route('id');

        return [
            'email'    => 'required|unique:'.config('permission.table_names.users', 'users').',email,'.$id,
            'last_name'     => 'required',
            'first_name'     => 'required',
            'password' => 'confirmed',
        ];
    }
}
