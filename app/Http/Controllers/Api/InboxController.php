<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use App\Inbox;
use App\LeaveRequests;


class InboxController extends Controller
{
    public function index(){
        $inbox = DB::table('inboxes')
                ->join('leave_requests','leave_requests.id','=','inboxes.id_leave_request')
                ->select('inboxes.*')
                ->get();

        if(count($inbox) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $inbox
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
            'id_leave_request'     => 'required',
            'unread'               => 'nullable',
        ]);

        if($validate->fails())
            return response (['message' => $validate->errors()],400);

        $inbox = Inbox::create($storeData);

        $inbox->unread = 1;

        return response([
            'message' => 'Add Inbox Success',
            'data' => $inbox,
        ],200);
    }

    public function destroy($id){
        $inbox = Inbox::find($id);

        if(is_null($inbox)){
            return response([
                'message' => 'Inbox Not Found',
                'data' => null
            ],404);
        }

        if($inbox->delete()){
            return response([
                'message' => 'Delete Inbox Success',
                'data' => $inbox,
            ],200);
        }
        
        return response([
            'message' => 'Delete Inbox Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $inbox = Inbox::find($id);
        if(is_null($inbox)){
            return response([
                'message' => 'Inbox Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'id_leave_request'     => 'required',
            'unread'               => 'nullable',
        ]);

        if($validate->fails())
        return response(['message' => $validate->errors()],400);

        $inbox->id_leave_request     = $updateData['id_leave_request'];
        $inbox->unread               = $updateData['unread'];
       
        if($inbox->save()){
            return response([
                'message' => 'Update Inbox Success',
                'data' => $inbox,
            ],200);
        }

        return response([
            'message' => 'Update Inbox Failed',
            'data' => null
        ],400);
    }

}
