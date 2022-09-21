<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RepairEmailRequest extends FormRequest
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
            'id' => 'required',
            'email' => 'required',
        ];
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes(){
        return [
            'id' => trans('repair.id'),
            'email' => trans('customer.email'),
        ];
    }
}
