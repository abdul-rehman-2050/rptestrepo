<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ModelRequest;
use App\Repositories\ModelRepository;
use App\Repositories\ActivityLogRepository;
use App\ItemType;
use Validator;
use App\Http\Requests\ImportRequest;
class ModelController extends Controller
{
    protected $repo;
    protected $request;
    protected $activity;
    protected $module = 'model';

    public function __construct(Request $request, ModelRepository $repo, ActivityLogRepository $activity)
    {
        $this->repo = $repo;
        $this->request = $request;
        $this->activity = $activity;
    }
    
    function buildTree(array &$elements, $parentId = 0) {
        $branch = array();
    
        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = $this->buildTree($elements, $element['id']);
                if ($children) {
                    $element['children'] = $children;
                }
                $branch[$element['id']] = $element;
                unset($elements[$element['id']]);
            }
        }
        return $branch;
    }

    public function preRequisite() {
        $this->middleware('permission:list-repair-category');


        $brands = ItemType::OnlyCategories()->get()->toArray();
        $rows = ItemType::all()->toArray();
        $items = (array)($this->buildTree($rows));
        return $this->success(compact('items', 'brands'));
    }

    public function fetchModels($id) {
        $models = ItemType::where('parent_id', $id)->get()->toArray();
        return $this->ok($models);
    }

    public function index() {

    }

    public function show($id)
    {
        return $this->ok($this->repo->findOrFail($id));
    }

    public function store(ModelRequest $request)
    {
        $this->middleware('permission:create-repair-category');

        $model = $this->repo->create($this->request);
        // $this->activity->record([
        //     'module' => $this->module,
        //     'module_id' => $model['id'],
        //     'activity' => 'added'
        // ]);

        $model['message'] = trans('model.added');
        return $this->success($model);
    }


    public function update(ModelRequest $request, $id) {
        $this->middleware('permission:edit-repair-category');

        $model = $this->repo->findOrFail($id);
        $model = $this->repo->update($model, $request);

        // $this->activity->record([
        //     'module' => $this->module,
        //     'sub_module' => $model->name,
        //     'activity' => 'updated'
        // ]);

        return $this->success(['message' => trans('model.updated')]);
    }

    public function destroy($id)
    {
        $this->middleware('permission:delete-repair-category');

        $item_type = \App\ItemType::where('parent_id', $id)->first();
        if($item_type){
            return $this->error(['message' => trans('model.cannot_delete_model_having_childs')]);
        }


        $model = $this->repo->findOrFail($id);
        $this->activity->record([
            'module' => $this->module,
            'module_id' => $model->id,
            'activity' => 'deleted'
        ]);

        $this->repo->delete($model);

        return $this->success(['message' => trans('model.deleted')]);
    }



    public function importManufacturers(ImportRequest $request)
    {
        $data = $request->csv;
        
        $rules_langs = [
            'name' => trans('customer.name'),
        ];

        $rules = [
            'name' => 'required|string',
        ];

        $import = [];
        $row_number = 1;
        foreach ($data as $row) {
            $validator = Validator::make($row, $rules, $rules_langs);
            if ($validator->passes()) {
                $row['created_by'] = \Auth::user()->id;
                $import[] = $row;
            } else {
                $error = [];
                foreach ($validator->errors()->messages() as $key => $validation) {
                    $error[$key] = '';
                    foreach ($validation as $msg) {
                        $error[$key] .= $msg . ' (on line ' .$row_number . ')';
                    }
                }
                return $this->error($error);
            }
        }

        if (!empty($import)) {
            foreach($import as $key => $item){
                $res = \App\ItemType::where('name', $item['name'])->first();
                if($res){
                    unset($import[$key]);
                }
            }
            if (\App\ItemType::insert($import)) {
                return $this->success(['message' => trans('model.import_manufacturer_completed')]);
            }
        }
        return $this->error(['message' => trans('model.import_manufacturer_complete_error')]);
    }


    public function importModelsBatch(ImportRequest $request)
    {
        $data = $request->csv;
        $id = $request->id > 0 ? $request->id : null;
        
        $rules_langs = [
            'name' => trans('model.name'),
        ];

        $rules = [
            'name' => 'required|string',
        ];

        if(!$id){
           $rules_langs['manufacturer'] = trans('model.manufacturer');
           $rules['manufacturer'] = 'required|string';
        }
        
        $import = [];
        $row_number = 1;
        foreach ($data as $row) {
            $validator = Validator::make($row, $rules, $rules_langs);
            if ($validator->passes()) {
                $row['created_by'] = \Auth::user()->id;
                $import[] = $row;
            } else {
                $error = [];
                foreach ($validator->errors()->messages() as $key => $validation) {
                    $error[$key] = '';
                    foreach ($validation as $msg) {
                        $error[$key] .= $msg . ' (on line ' .$row_number . ')';
                    }
                }
                return $this->error($error);
            }
        }

        if (!empty($import)) {
            if($id){
                $manufacturer = \App\ItemType::where('id', $id)->first();
            }
            foreach($import as $key => $item){
                if(!$id){
                    $manufacturer = \App\ItemType::where('name', $item['manufacturer'])->first();
                    if($manufacturer){
                        unset($import[$key]['manufacturer']);
                        $import[$key]['parent_id'] = $manufacturer->id;
                    }else{
                        $manufacturer = \App\ItemType::create([
                            'created_by' => \Auth::user()->id,
                            'name' => $import[$key]['manufacturer']
                        ]);
                    }
                }else{
                    $import[$key]['parent_id'] = $id;
                }
                $model = \App\ItemType::where('parent_id', $manufacturer->id)->where('name', $item['name'])->first();
                if($model){
                    unset($import[$key]);
                }
            }
            if (\App\ItemType::insert($import)) {
                return $this->success(['message' => trans('model.import_model_completed')]);
            }
        }
        return $this->error(['message' => trans('model.import_model_complete_error')]);
    }

    
}
