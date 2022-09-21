<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModelRequest extends FormRequest
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
                'type' => 'required',
                'name' => 'required',
                'manufacturer_id' => 'required_if:type,model',
            ];
        } else if ($this->method() === 'PATCH') {
            return [
                'type' => 'required',
                'name' => 'required',
                'manufacturer_id' => 'required_if:type,model',
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
            'name' => trans('category.name'),
            'type' => trans('tax_rate.type'),
            'manufacturer_id' => trans('repair.manufacturer'),
        ];
    }
}
