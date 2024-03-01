<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use App\Icon;


class IconController extends Controller
{
    public function index(){
        $icon = Icon::all();

        if(count($icon) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $icon
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }


    public function show($id){
        $icon = Icon::find($id);

        if(!is_null($icon)){
            return response([
                'message' => 'Retrieve Icon Success',
                'data' => $icon
            ],200);
        }

        return response([
            'message' => 'Icon Not Found',
            'data' => null
        ],400);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'icon_name'     => 'required',
        ]);

        if($validate->fails())
            return response (['message' => $validate->errors()],400);

        $icon = Icon::create($storeData);
        return response([
            'message' => 'Add Icon Success',
            'data' => $icon,
        ],200);
    }

    public function destroy($id){
        $icon = Icon::find($id);

        if(is_null($icon)){
            return response([
                'message' => 'Icon Not Found',
                'data' => null
            ],404);
        }

        if($icon->delete()){
            return response([
                'message' => 'Delete Icon Success',
                'data' => $icon,
            ],200);
        }
        
        return response([
            'message' => 'Delete Icon Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $icon = Icon::find($id);
        if(is_null($icon)){
            return response([
                'message' => 'Icon Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'icon_name'     => 'required',
        ]);

        if($validate->fails())
        return response(['message' => $validate->errors()],400);

        $icon->icon_name     = $updateData['icon_name'];
       
        if($icon->save()){
            return response([
                'message' => 'Update Icon Success',
                'data' => $icon,
            ],200);
        }

        return response([
            'message' => 'Update Icon Failed',
            'data' => null
        ],400);
    }

}
