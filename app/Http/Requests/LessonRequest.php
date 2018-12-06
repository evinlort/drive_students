<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Factory as ValidationFactory;

class LessonRequest extends FormRequest
{

    public function __construct(ValidationFactory $validationFactory) {
    
        $validationFactory->extend(
            'check_lessons',
            function ($attribute, $value, $parameters) {
                // return false;
                
            },
            'Cannot perform transaction for this IBAN, country is blocked'
        );
    }
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
            'date_n_times'=> 'check_lessons',
        ];
    }
}
