<?php
namespace App\Repositories;

use App\PaymentMethod;
use Illuminate\Validation\ValidationException;

class PaymentMethodRepository
{
    protected $payment_method;

    public function __construct(PaymentMethod $payment_method)
    {
        $this->payment_method = $payment_method;
    }


    public function listPaymentMethods()
    {
        return $this->payment_method->select('name', 'id')->get()->toArray();
    }


    public function create($request, $register = 0)
    {

        $params = $request->all();
        $params['created_by'] = \Auth::user()->id;
        $payment_method = $this->payment_method->forceCreate($params);
        return $payment_method;
    }


    public function update(PaymentMethod $payment_method, $params)
    {
        $params = $params->all();
        $payment_method->forceFill($params, 'update')->save();
        return $payment_method;
    }

       
    public function findOrFail($id)
    {
        $payment_method = $this->payment_method->find($id);

        if (! $payment_method) {
            throw ValidationException::withMessages(['message' => trans('payment_method.could_not_find')]);
        }

        return $payment_method;
    }

    public function delete(PaymentMethod $payment_method)
    {
        return $payment_method->delete();
    }

    public function deleteMultiple($ids)
    {
        return $this->payment_method->whereIn('id', $ids)->delete();
    }
}
