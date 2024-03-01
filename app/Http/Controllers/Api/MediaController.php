<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use App\Media;


class MediaController extends Controller
{
    public function index(){
        $media = Media::all();

        if(count($media) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $media
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }


    public function show($id){
        $media = Media::find($id);

        if(!is_null($media)){
            return response([
                'message' => 'Retrieve Media Success',
                'data' => $media
            ],200);
        }

        return response([
            'message' => 'Media Not Found',
            'data' => null
        ],400);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_leave_request'     => 'required',
            'file_name'            => 'nullable',
            //|file|image|mimes:jpeg,png,jpg,gif,svg
        ]);

        if($validate->fails())
            return response (['message' => $validate->errors()],400);

        if (!is_null($request->file('file_name'))) {
                $file          = $request->file('file_name');
                $nama_file     = time() . "_" . $file->getClientOriginalName();
                $tujuan_upload = 'file_name';
                $file->move($tujuan_upload, $nama_file);
        } else {
            $nama_file = 'No image';
        }

        $media = new Media();
        
        $media->id_leave_request           = $storeData['id_leave_request'];
        $media->file_name                  = $nama_file;
        
        $media->save();

        //$media = Media::create($storeData);
        

        return response([
            'message' => 'Add Media Success',
            'data' => $media,
        ],200);
    }

    public function destroy($id){
        $media = Media::find($id);

        if(is_null($media)){
            return response([
                'message' => 'Media Not Found',
                'data' => null
            ],404);
        }

        if($media->delete()){
            return response([
                'message' => 'Delete Media Success',
                'data' => $media,
            ],200);
        }
        
        return response([
            'message' => 'Delete Media Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $media = Media::find($id);
        if(is_null($media)){
            return response([
                'message' => 'Media Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'id_leave_request'     => 'required',
            'file_name'            => 'required',
        ]);

        if($validate->fails())
        return response(['message' => $validate->errors()],400);

        $media->id_leave_request     = $updateData['id_leave_request'];
        $media->file_name            = $updateData['file_name'];

        if (!is_null($request->file('file_name'))) {
            
            $file          = $request->file('file_name');
            $nama_file     = time() . "_" . $file->getClientOriginalName();
            $tujuan_upload = 'file_name';
            $file->move($tujuan_upload, $nama_file);
    
            $media->file_name     = $nama_file;
        }
       
        if($media->save()){
            return response([
                'message' => 'Update Media Success',
                'data' => $media,
            ],200);
        }

        return response([
            'message' => 'Update Media Failed',
            'data' => null
        ],400);
    }
    
}
