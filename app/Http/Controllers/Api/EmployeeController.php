<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use App\Employee;
use App\User;


class EmployeeController extends Controller
{
    public function index(){
        $employees = DB::table('employees')
                    ->join('users','users.id','=','employees.id_user')
                    ->join('seniors','seniors.id','=','employees.id_senior')
                    ->join('regions','regions.id','=','employees.id_region')
                    ->join('facilities','facilities.id','=','employees.id_facility')
                    ->join('departments','departments.id','=','employees.id_department')
                    ->select('employees.*','users.name','seniors.senior_name','regions.country','facilities.item','departments.department_name')
                    ->get();

        if(count($employees) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $employees
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }


    public function show($id){
        $employees = Employee::find($id);

        if(!is_null($employees)){
            return response([
                'message' => 'Retrieve Employee Success',
                'data' => $employees
            ],200);
        }

        return response([
            'message' => 'Employee Not Found',
            'data' => null
        ],400);
    }

    public function showStructure(){
        $employees = DB::table('employees')
                    ->join('users','users.id','=','employees.id_user')
                    ->join('seniors','seniors.id','=','employees.id_senior')
                    ->join('regions','regions.id','=','employees.id_region')
                    ->join('facilities','facilities.id','=','employees.id_facility')
                    ->join('departments','departments.id','=','employees.id_department')
                    ->select('employees.id','employees.id_senior','users.name','seniors.senior_name','regions.country','employees.title','departments.department_name','users.profile_picture')
                    ->get();

        if(count($employees) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $employees
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_user'                   => 'required',
            'id_senior'                 => 'required',
            'id_region'                 => 'required',
            'id_facility'               => 'required',
            'id_department'             => 'required',
            'title'                     => 'required',
            'holidays_total'            => 'required',
            'sick_total'                => 'required',
            'maternity_total'           => 'required',
            'paternity_total'           => 'required',
            'employee_start_date'       => 'required|date_format:Y-m-d',
            
        ]);

        if($validate->fails())
            return response (['message' => $validate->errors()],400);

        $employees = Employee::create($storeData);

        $user = User::find($employees->id_user);

        $user->verify = 1;
        $user->save();

        return response([
            'message' => 'Add Employee Success',
            'data' => $employees,
        ],200);
    }

    public function destroy($id){
        $employees = Employee::find($id);

        if(is_null($employees)){
            return response([
                'message' => 'Employee Not Found',
                'data' => null
            ],404);
        }

        if($employees->delete()){
            return response([
                'message' => 'Delete Employee Success',
                'data' => $employees,
            ],200);
        }
        
        return response([
            'message' => 'Delete Employee Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $employees = Employee::find($id);
        if(is_null($employees)){
            return response([
                'message' => 'Employee Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'id_user'                   => 'required',
            'id_senior'                 => 'required',
            'id_region'                 => 'required',
            'id_facility'               => 'required',
            'id_department'             => 'required',
            'title'                     => 'required',
            'holidays_total'            => 'required',
            'sick_total'                => 'required',
            'maternity_total'           => 'required',
            'paternity_total'           => 'required',
            'employee_start_date'       => 'required|date_format:Y-m-d',
        ]);

        if($validate->fails())
        return response(['message' => $validate->errors()],400);

        $employees->id_user                   = $updateData['id_user'];
        $employees->id_senior                 = $updateData['id_senior'];
        $employees->id_region                 = $updateData['id_region'];
        $employees->id_facility               = $updateData['id_facility'];
        $employees->id_department             = $updateData['id_department'];
        $employees->title                     = $updateData['title'];
        $employees->holidays_total            = $updateData['holidays_total'];
        $employees->sick_total                = $updateData['sick_total'];
        $employees->maternity_total           = $updateData['maternity_total'];
        $employees->paternity_total           = $updateData['paternity_total'];
        $employees->employee_start_date       = $updateData['employee_start_date'];

        
        if($employees->save()){
            return response([
                'message' => 'Update Employee Success',
                'data' => $employees,
            ],200);
        }

        return response([
            'message' => 'Update Employee Failed',
            'data' => null
        ],400);
    }
    
}
