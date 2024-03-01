<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use App\Holiday;


class HolidayController extends Controller
{
    public function index(){
        $holiday = DB::table('holidays')
                    ->join('regions','regions.id','=','holidays.id_region')
                    ->select('holidays.*','regions.country')
                    ->get();

        if(count($holiday) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $holiday
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }


    public function show($id){
        $holiday = Holiday::find($id);

        if(!is_null($holiday)){
            return response([
                'message' => 'Retrieve Holiday Success',
                'data' => $holiday
            ],200);
        }

        return response([
            'message' => 'Holiday Not Found',
            'data' => null
        ],400);
    }

    public function store(Request $request){
        $storeData = $request->all();
    
        $validate = Validator::make($storeData, [
            'id_region'           => 'nullable',
            'title'               => 'nullable',
            'holiday_date'        => 'nullable',
            'description'         => 'nullable',
            'year'                => 'nullable',
        ]);

        if($validate->fails())
            return response (['message' => $validate->errors()],400);

        // $id_region = $request->id_region;
        // foreach ((array)$id_region as $region_id) {
        //     $values[] = [
        //         'id_region'         => $region_id,
        //         'title'             => $request->title,
        //         'holiday_date'      => $request->holiday_date,
        //         'description'       => $request->description,
        //         'year'              => $request->year,
        //     ];

        //     $holiday = Holiday::create($storeData);
            
        //     return response([
        //         'message' => 'Add Holiday Success',
        //         'data' => $holiday,
        //     ],200);
        // }

        foreach ((array)$request->id_region as $region_id) {
            
            $holiday = new Holiday;

            $holiday->id_region     = $region_id;
            $holiday->title         = $request->title;
            $holiday->holiday_date  = $request->holiday_date;
            $holiday->description   = $request->description;
            $holiday->year          = $request->year;

            $holiday->save();
        }

        return response([
            'message' => 'Add Holiday Success',
            'data' => $holiday,
        ],200);


        //$holiday = Holiday::create($storeData);

        // return response([
        //     'message' => 'Add Holiday Success',
        //     'data' => $holiday,
        // ],200);
       
    }

    public function destroy($id){
        $holiday = Holiday::find($id);

        if(is_null($holiday)){
            return response([
                'message' => 'Holiday Not Found',
                'data' => null
            ],404);
        }

        if($holiday->delete()){
            return response([
                'message' => 'Delete Holiday Success',
                'data' => $holiday,
            ],200);
        }
        
        return response([
            'message' => 'Delete Holiday Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $holiday = Holiday::find($id);
        if(is_null($holiday)){
            return response([
                'message' => 'Holiday Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'id_region'           => 'nullable',
            'title'               => 'nullable',
            'holiday_date'        => 'nullable|date_format:Y-m-d',
            'description'         => 'nullable',
            'year'                => 'nullable',
        ]);

        if($validate->fails())
        return response(['message' => $validate->errors()],400);

        $holiday->id_region         = $updateData['id_region'];
        $holiday->title             = $updateData['title'];
        $holiday->holiday_date      = $updateData['holiday_date'];
        $holiday->description       = $updateData['description'];
        $holiday->year              = $updateData['year'];

        
        if($holiday->save()){
            return response([
                'message' => 'Update Holiday Success',
                'data' => $holiday,
            ],200);
        }

        return response([
            'message' => 'Update Holiday Failed',
            'data' => null
        ],400);
    }
    
}
