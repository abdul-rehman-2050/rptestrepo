<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StoreRequest;
use App\Repositories\StoreRepository;
use App\Repositories\ActivityLogRepository;
use App\Store;

class StoreController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;

    protected $module = 'store';

    public function __construct(Request $request, StoreRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;
    }

    public function preRequisite()
    {
        $store_count = \App\Store::count();
        $users = \App\User::GetExcludeRole(1)
            ->selectRaw('users.id as value, CONCAT(first_name, " ", last_name) as text')
            ->leftJoin('profiles', 'profiles.user_id', '=', 'users.id')
            ->get();
        return $this->success(compact('store_count', 'users'));
    }


  

    public function index()
    {
        $this->middleware('permission:list-store');
        $query = Store::search()
                ->selectRaw('id, name, address, phone, email');

        return dataTable()->query($query)
            ->setFilters(
                'store_rates.name'
            )
            ->get();
    }

     public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    public function store(StoreRequest $request)
    {
        $this->middleware('permission:create-store');

        $store = $this->repo->create($this->request);

        $this->activity->record([
            'module' => $this->module,
            'module_id' => $store->id,
            'activity' => 'added'
        ]);

        return $this->success(['message' => trans('store.added')]);
    }


    public function update(StoreRequest $request, $id) {
        $this->middleware('permission:edit-store');

        $store = $this->repo->findOrFail($id);
        $store = $this->repo->update($store, $request);

        $this->activity->record([
            'module' => $this->module,
            'sub_module' => $store->name,
            'activity' => 'updated'
        ]);

        return $this->success(['message' => trans('store.updated')]);
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-store');

        $store = $this->repo->findOrFail($id);


        $this->activity->record([
            'module' => $this->module,
            'module_id' => $store->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($store);

        return $this->success(['message' => trans('store.deleted')]);
    }

}
