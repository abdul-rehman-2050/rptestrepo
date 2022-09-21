<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * Used to return success response
     * @return Response
     */

    public function ok($items = null, $encode_number = false)
    {
        if($encode_number) {
            return response()->json($items)->setEncodingOptions(JSON_NUMERIC_CHECK);
        }
        return response()->json($items);
    }
    
    /**
     * Used to return success response
     * @return Response
     */

    public function success($items = null, $status = 200, $encode_number = true)
    {
        $data = ['status' => 'success'];

        if ($items instanceof Arrayable) {
            $items = $items->toArray();
        }

        if ($items) {
            foreach ($items as $key => $item) {
                $data[$key] = $item;
            }
        }

        if($encode_number) {
            return response()->json($data, $status)->setEncodingOptions(JSON_NUMERIC_CHECK);
        }
        return response()->json($data, $status);
    }

    /**
     * Used to return error response
     * @return Response
     */

    public function error($items = null, $status = 422, $encode_number = true)
    {
        $data = array();

        if ($items) {
            foreach ($items as $key => $item) {
                $data['errors'][$key][] = $item;
            }
        }

        
        if($encode_number) {
            return response()->json($data, $status)->setEncodingOptions(JSON_NUMERIC_CHECK);
        }
        return response()->json($data, $status);
    }
}
