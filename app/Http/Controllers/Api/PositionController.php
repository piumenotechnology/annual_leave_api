<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use App\Position;


class PositionController extends Controller
{
    public function index(){
        $position = Position::all();

        if(count($position) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $position
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }


    public function show($id){
        $position = Position::find($id);

        if(!is_null($position)){
            return response([
                'message' => 'Retrieve Position Success',
                'data' => $position
            ],200);
        }

        return response([
            'message' => 'Position Not Found',
            'data' => null
        ],400);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'position'     => 'required',
        ]);

        if($validate->fails())
            return response (['message' => $validate->errors()],400);

        $position = Position::create($storeData);
        return response([
            'message' => 'Add Position Success',
            'data' => $position,
        ],200);
    }

    public function destroy($id){
        $position = Position::find($id);

        if(is_null($position)){
            return response([
                'message' => 'Position Not Found',
                'data' => null
            ],404);
        }

        if($position->delete()){
            return response([
                'message' => 'Delete Position Success',
                'data' => $position,
            ],200);
        }
        
        return response([
            'message' => 'Delete Position Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $position = Position::find($id);
        if(is_null($position)){
            return response([
                'message' => 'Position Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'position'     => 'required',
        ]);

        if($validate->fails())
        return response(['message' => $validate->errors()],400);

        $position->position     = $updateData['position'];
       
        if($position->save()){
            return response([
                'message' => 'Update Position Success',
                'data' => $position,
            ],200);
        }

        return response([
            'message' => 'Update Position Failed',
            'data' => null
        ],400);
    }
    
}
