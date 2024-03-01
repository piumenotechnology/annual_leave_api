<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use App\Setting;


class SettingController extends Controller
{
    public function index(){
        $setting = Setting::all();

        if(count($setting) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $setting
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }


    public function show($id){
        $setting = Setting::find($id);

        if(!is_null($setting)){
            return response([
                'message' => 'Retrieve Setting Success',
                'data' => $setting
            ],200);
        }

        return response([
            'message' => 'Setting Not Found',
            'data' => null
        ],400);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'company_name'          => 'required',
            'logo'                  => 'required|file|image|mimes:jpeg,png,jpg,gif,svg',
            'address'               => 'required',
            'theme_configuration'   => 'required',
            'phone_number'          => 'required',
            'company_email'         => 'required',
        ]);

        if($validate->fails())
            return response (['message' => $validate->errors()],400);

            if (!is_null($request->file('logo'))) {
                $file          = $request->file('logo');
                $nama_file     = time() . "_" . $file->getClientOriginalName();
                $tujuan_upload = 'logo';
                $file->move($tujuan_upload, $nama_file);
            } else {
                $nama_file = 'NoImage.png';
            }

        $setting = new Setting();
        
        $setting->company_name           = $storeData['company_name'];
        $setting->logo                   = $nama_file;
        $setting->address                = $storeData['address'];
        $setting->theme_configuration    = $storeData['theme_configuration'];
        $setting->phone_number           = $storeData['phone_number'];
        $setting->company_email          = $storeData['company_email'];
        
        $setting->save();

        //$setting = Setting::create($storeData);
        return response([
            'message' => 'Add Setting Success',
            'data' => $setting,
        ],200);
    }

    public function destroy($id){
        $setting = Setting::find($id);

        if(is_null($setting)){
            return response([
                'message' => 'Setting Not Found',
                'data' => null
            ],404);
        }

        if($setting->delete()){
            return response([
                'message' => 'Delete Setting Success',
                'data' => $setting,
            ],200);
        }
        
        return response([
            'message' => 'Delete Setting Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $setting = Setting::find($id);
        if(is_null($setting)){
            return response([
                'message' => 'Setting Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'company_name'          => 'required',
            'logo'                  => 'required|file|image|mimes:jpeg,png,jpg,gif,svg',
            'address'               => 'required',
            'theme_configuration'   => 'required',
            'phone_number'          => 'required',
            'company_email'         => 'required',
        ]);

        if($validate->fails())
        return response(['message' => $validate->errors()],400);

        $setting->company_name                = $updateData['company_name'];
        $setting->address                     = $updateData['address'];
        $setting->theme_configuration         = $updateData['theme_configuration'];
        $setting->phone_number                = $updateData['phone_number'];
        $setting->company_email               = $updateData['company_email'];

        if (!is_null($request->file('logo'))) {
            $file          = $request->file('logo');
            $nama_file     = time() . "_" . $file->getClientOriginalName();
            $tujuan_upload = 'logo';
            $file->move($tujuan_upload, $nama_file);
    
            $setting->logo     = $nama_file;
        }
       
        if($setting->save()){
            return response([
                'message' => 'Update Setting Success',
                'data' => $setting,
            ],200);
        }

        return response([
            'message' => 'Update Setting Failed',
            'data' => null
        ],400);
    }
    
}
