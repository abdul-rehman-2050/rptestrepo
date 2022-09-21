<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PaymentMethodRequest;
use App\Repositories\PaymentMethodRepository;
use App\Repositories\ActivityLogRepository;
use App\PaymentMethod;

class PaymentMethodController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;

    protected $module = 'payment_method';

    public function __construct(Request $request, PaymentMethodRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;

    }
  

    public function index()
    {
        $this->middleware('permission:list-payment-method');
        $query = PaymentMethod::search()
                ->selectRaw('id, code, name, description');

        return dataTable()->query($query)
            ->setFilters(
                'payment_methods.name', 'payment_methods.description'
            )
            ->get();
    }

     public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    public function store(PaymentMethodRequest $request)
    {
        $this->middleware('permission:create-payment-method');

        $payment_method = $this->repo->create($this->request);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $payment_method->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('payment_method.added')]);
    }


    public function update(PaymentMethodRequest $request, $id) {
        $this->middleware('permission:edit-payment-method');

        $payment_method = $this->repo->findOrFail($id);
        $payment_method = $this->repo->update($payment_method, $request);

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $payment_method->name,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('payment_method.updated')]);
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-payment-method');

        $payment_method = $this->repo->findOrFail($id);


        $this->activity->record([
            'module' => $this->module,
            'module_id' => $payment_method->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($payment_method);

        return $this->success(['message' => trans('payment_method.deleted')]);
    }

}
