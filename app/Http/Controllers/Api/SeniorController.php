<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use App\Senior;


class SeniorController extends Controller
{
    public function index(){
        $senior = DB::table('seniors')
                    ->join('positions','positions.id','=','seniors.id_position')
                    ->join('departments','departments.id','=','seniors.id_department')
                    ->select('seniors.*','positions.position','departments.department_name')
                    ->get();

        if(count($senior) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $senior
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }


    public function show($id){
        $senior = Senior::find($id);

        if(!is_null($senior)){
            return response([
                'message' => 'Retrieve Senior Success',
                'data' => $senior
            ],200);
        }

        return response([
            'message' => 'Senior Not Found',
            'data' => null
        ],400);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_department'      => 'required',
            'id_position'        => 'required',
            'senior_name'        => 'required',
            'senior_email'       => 'required',
        ]);

        if($validate->fails())
            return response (['message' => $validate->errors()],400);

        $senior = Senior::create($storeData);
        return response([
            'message' => 'Add Senior Success',
            'data' => $senior,
        ],200);
    }

    public function destroy($id){
        $senior = Senior::find($id);

        if(is_null($senior)){
            return response([
                'message' => 'Senior Not Found',
                'data' => null
            ],404);
        }

        if($senior->delete()){
            return response([
                'message' => 'Delete Senior Success',
                'data' => $senior,
            ],200);
        }
        
        return response([
            'message' => 'Delete Senior Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $senior = Senior::find($id);
        if(is_null($senior)){
            return response([
                'message' => 'Senior Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'id_department'      => 'required',
            'id_position'        => 'required',
            'senior_name'        => 'required',
            'senior_email'       => 'required',
        ]);

        if($validate->fails())
        return response(['message' => $validate->errors()],400);

        $senior->id_department      = $updateData['id_department'];
        $senior->id_position        = $updateData['id_position'];
        $senior->senior_name        = $updateData['senior_name'];
        $senior->senior_email       = $updateData['senior_email'];
        
        
        if($senior->save()){
            return response([
                'message' => 'Update Senior Success',
                'data' => $senior,
            ],200);
        }

        return response([
            'message' => 'Update Senior Failed',
            'data' => null
        ],400);
    }
    
}
