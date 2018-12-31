<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminStudentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'identity' => 'required|unique:users,identity',
            'full_name' => 'required',
            'weeks' => 'required',
            'lessons' => 'required'
        ];
    }

    public function messages() {
        return [
            'identity.unique' => __('The identity is not so unique!'),
        ];
    }
}
