<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentMethodRequest extends FormRequest
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
        $id = $this->route('id');
        if ($this->method() === 'POST') {
            return [
                'code' => 'required|unique:payment_methods,code,'.$id.',id',
                'name' => 'required',
                'description' => 'present',
            ];
        } else if ($this->method() === 'PATCH') {
            return [
                'code' => 'required|unique:payment_methods,code,'.$id.',id',
                'description' => 'present',
                'name' => 'required',
            ];
        }
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'code' => trans('payment_method.code'),
            'name' => trans('payment_method.name'),
            'description' => trans('payment_method.description'),
        ];
    }
}
