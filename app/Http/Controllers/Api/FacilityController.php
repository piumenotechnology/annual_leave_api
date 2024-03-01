<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use App\Facility;


class FacilityController extends Controller
{
    public function index(){
        $facility = Facility::all();

        if(count($facility) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $facility
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }


    public function show($id){
        $facility = Facility::find($id);

        if(!is_null($facility)){
            return response([
                'message' => 'Retrieve Facility Success',
                'data' => $facility
            ],200);
        }

        return response([
            'message' => 'Facility Not Found',
            'data' => null
        ],400);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'item'                     => 'required',
            'quantity'                 => 'required',
            'facility_description'     => 'required',
        ]);

        if($validate->fails())
            return response (['message' => $validate->errors()],400);

        $facility = Facility::create($storeData);
        return response([
            'message' => 'Add Facility Success',
            'data' => $facility,
        ],200);
    }

    public function destroy($id){
        $facility = Facility::find($id);

        if(is_null($facility)){
            return response([
                'message' => 'Facility Not Found',
                'data' => null
            ],404);
        }

        if($facility->delete()){
            return response([
                'message' => 'Delete Facility Success',
                'data' => $facility,
            ],200);
        }
        
        return response([
            'message' => 'Delete Facility Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $facility = Facility::find($id);
        if(is_null($facility)){
            return response([
                'message' => 'Facility Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'item'                     => 'required',
            'quantity'                 => 'required',
            'facility_description'     => 'required',
        ]);

        if($validate->fails())
        return response(['message' => $validate->errors()],400);

        $facility->item                     = $updateData['item'];
        $facility->quantity                 = $updateData['quantity'];
        $facility->facility_description     = $updateData['facility_description'];
       
        if($facility->save()){
            return response([
                'message' => 'Update Facility Success',
                'data' => $facility,
            ],200);
        }

        return response([
            'message' => 'Update Facility Failed',
            'data' => null
        ],400);
    }
    
}
