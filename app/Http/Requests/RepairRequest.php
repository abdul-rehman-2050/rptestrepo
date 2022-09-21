<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RepairRequest extends FormRequest
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

        $data = [
            'serial_number' => 'present',
            'customer_id' => 'required',
            'category' => 'present',
            'assigned_to' => 'present',
            'manufacturer' => 'present',
            'model' => 'present',
            'defect' => 'present',
            'service_charges' => 'present|numeric',
            'expected_close_date' => 'present',
            'has_warranty' => 'present',
            'warranty_period' => 'present',
            'comments' => 'present',
            'diagnostics' => 'present',
            'intake_signature' => 'present',
            'items' => 'present',
            'attachments' => 'present',
            'repair_toggles' => 'present',
            'pattern' => 'present',
            'code' => 'required',
            'status_id' => 'required',
            'send_sms' => 'present',
            'send_email' => 'present',
            'taxrate_id' => 'present',
            'imei' => 'present',
            'payment' => 'present',
        ];
        return $data;
    }

    /**
     * Translate fields with user friendly name.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'serial_number' => trans('repair.serial_number'),
        ];
    }
}
