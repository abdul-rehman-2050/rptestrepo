<?php
namespace App\Repositories;

use App\ItemType;
use Illuminate\Validation\ValidationException;

class ModelRepository
{
    protected $model;

    public function __construct(ItemType $model)
    {
        $this->model = $model;
    }

    public function listParentCategories()
    {
        return $this->model->OnlyCategories()->get()->pluck('name', 'id')->all();
    }

    public function listSubCategoriesByID($id, $query)
    {
        return $this->model->where('parent_id', $id)->get()->pluck('name', 'id')->all();
    }

    
    public function create($request, $register = 0)
    {
        $params = $request->all();

        $data = [
            'name' => $params['name'],
            'description' => $params['description'] ? : null,
            'parent_id' => null,
        ];

        if (isset($params['manufacturer_id']) && $params['manufacturer_id'] > 0) {
            $data['parent_id'] = $params['manufacturer_id'];
        }

        $data['created_by'] = \Auth::user()->id;
        $model = $this->model->forceCreate($data);


        if($params['type'] == 'manufacturer') {
            $parent = \App\ItemType::find($data['parent_id']);
            return array('manufacturer_id'=>$model->id, 'manufacturer'=>$data['name']);
        }else{
            $manufacturer = \App\ItemType::find($params['manufacturer_id']);
            
            return array(
                'id'=>$model->id, 
                'name'=>$data['name'], 
                'manufacturer_id'=>$manufacturer->id,
                'manufacturer'=>$manufacturer->name,
            );
        }
        
    }


    public function update(ItemType $model, $request)
    {
        $params = $request->all();

        $data = [
            'name' => $params['name'],
            'description' => $params['description'] ? : null,
            'parent_id' => null,
        ];

        if ($params['manufacturer_id'] > 0) {
            $data['parent_id'] = $params['manufacturer_id'];
        }

        $data['created_by'] = \Auth::user()->id;
        $model->forceFill($data, 'update')->save();


        if($params['type'] == 'manufacturer') {
            $parent = \App\ItemType::find($data['parent_id']);
            return array('manufacturer_id'=>$model->id, 'manufacturer'=>$data['name']);
        }else{
            $manufacturer = \App\ItemType::find($params['manufacturer_id']);
            
            return array(
                'id'=>$model->id, 
                'name'=>$data['name'], 
                'manufacturer_id'=>$manufacturer->id,
                'manufacturer'=>$manufacturer->name,
            );
        }
        return $model;
    }

       
    public function findOrFail($id)
    {
        $model = $this->model->find($id);

        if (! $model) {
            throw ValidationException::withMessages(['message' => trans('model.could_not_find')]);
        }

        return $model;
    }

    public function delete(ItemType $model)
    {
        return $model->delete();
    }

    public function deleteMultiple($ids)
    {
        return $this->model->whereIn('id', $ids)->delete();
    }
}
