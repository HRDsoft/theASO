<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserStoreCrudRequest extends FormRequest
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
            //
        // ];
        return [
            'email'    => 'required|unique:'.config('permission.table_names.users', 'users').',email',
            'last_name'     => 'required',
            'first_name'     => 'required',
            'password' => 'required|confirmed',
        ];
    }
}
