<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use App\Department;


class DepartmentController extends Controller
{
    public function index(){
        $department = Department::all();

        if(count($department) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $department
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }


    public function show($id){
        $department = Department::find($id);

        if(!is_null($department)){
            return response([
                'message' => 'Retrieve Department Success',
                'data' => $department
            ],200);
        }

        return response([
            'message' => 'Department Not Found',
            'data' => null
        ],400);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'department_name'     => 'required',
        ]);

        if($validate->fails())
            return response (['message' => $validate->errors()],400);

        $department = Department::create($storeData);
        return response([
            'message' => 'Add Department Success',
            'data' => $department,
        ],200);
    }

    public function destroy($id){
        $department = Department::find($id);

        if(is_null($department)){
            return response([
                'message' => 'Department Not Found',
                'data' => null
            ],404);
        }

        if($department->delete()){
            return response([
                'message' => 'Delete Department Success',
                'data' => $department,
            ],200);
        }
        
        return response([
            'message' => 'Delete Department Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $department = Department::find($id);
        if(is_null($department)){
            return response([
                'message' => 'Department Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'department_name'     => 'required',
        ]);

        if($validate->fails())
        return response(['message' => $validate->errors()],400);

        $department->department_name     = $updateData['department_name'];
       
        if($department->save()){
            return response([
                'message' => 'Update Department Success',
                'data' => $department,
            ],200);
        }

        return response([
            'message' => 'Update Department Failed',
            'data' => null
        ],400);
    }
        
}

    
    

