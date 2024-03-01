<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use App\Region;


class RegionController extends Controller
{
    public function index(){
        $region = Region::all();

        if(count($region) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $region
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }


    public function show($id){
        $region = Region::find($id);

        if(!is_null($region)){
            return response([
                'message' => 'Retrieve Region Success',
                'data' => $region
            ],200);
        }

        return response([
            'message' => 'Region Not Found',
            'data' => null
        ],400);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'country'     => 'required',
        ]);

        if($validate->fails())
            return response (['message' => $validate->errors()],400);

        $region = Region::create($storeData);
        return response([
            'message' => 'Add Region Success',
            'data' => $region,
        ],200);
    }

    public function destroy($id){
        $region = Region::find($id);

        if(is_null($region)){
            return response([
                'message' => 'Region Not Found',
                'data' => null
            ],404);
        }

        if($region->delete()){
            return response([
                'message' => 'Delete Region Success',
                'data' => $region,
            ],200);
        }
        
        return response([
            'message' => 'Delete Region Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $region = Region::find($id);
        if(is_null($region)){
            return response([
                'message' => 'Region Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'country'     => 'required',
        ]);

        if($validate->fails())
        return response(['message' => $validate->errors()],400);

        $region->country     = $updateData['country'];
       
        if($region->save()){
            return response([
                'message' => 'Update Region Success',
                'data' => $region,
            ],200);
        }

        return response([
            'message' => 'Update Region Failed',
            'data' => null
        ],400);
    }
    
}
