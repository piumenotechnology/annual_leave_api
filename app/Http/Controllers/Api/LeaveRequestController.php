<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Validator;
use App\LeaveRequest;
use App\User;
use App\Senior;
use App\Inbox;
use App\Mail\LeaveMail;
//use Mail;
use Illuminate\Support\Facades\Mail;


class LeaveRequestController extends Controller
{
    public function index(){
        $leaverequest = DB::table('leave_requests')
                    ->join('users','users.id','=','leave_requests.id_user')
                    ->select('leave_requests.*','users.name')
                    ->get();

        if(count($leaverequest) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $leaverequest
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }


    public function show($id){
        $leaverequest = LeaveRequest::find($id);

        if(!is_null($leaverequest)){
            return response([
                'message' => 'Retrieve Leave Request Success',
                'data' => $leaverequest
            ],200);
        }

        return response([
            'message' => 'Leave Request Not Found',
            'data' => null
        ],400);
    }

    public function store(Request $request){
        $storeData = $request->all();
        $validate = Validator::make($storeData, [
            'id_user'                   => 'required',
            'id_senior'                 => 'required',
            'start_date'                => 'required|date_format:Y-m-d',
            'end_date'                  => 'required|date_format:Y-m-d',
            'total_date'                => 'nullable',
            'request_type'              => 'required|in:Holiday,Sick,Maternity,Paternity',
            'day_time'                  => 'required|in:Fulltime,AM,PM',
            'overall_status'            => 'nullable',
            'approval_status'           => 'nullable',
            'document'                  => 'required',
            //|file|mimes:pdf,jpeg,png,jpg,gif,svg
        ]);

        if($validate->fails())
            return response (['message' => $validate->errors()],400);

            // $start_date = $request->start_date;
            // $end_date = $request->end_date;
            // $startdate = \Carbon\Carbon::parse($start_date);
            // $enddate = \Carbon\Carbon::parse($end_date);
            // $daydiff = $startdate->diffInDays($enddate);

            // //$user = User::find($leaverequest->id_user);
            
            // $checkRemainingHolidays = LeaveRequest::join('users','users.id','=','leave_requests.id_user')
            //                           ->whereRaw('users.id = '.$request->id_user)
            //                           ->value('holiday_total');
            
            // $checkRemainingSicks = LeaveRequest::join('users','users.id','=','leave_requests.id_user')
            //                           ->whereRaw('users.id = '.$request->id_user)
            //                           ->value('sick_total');

            // $checkRemainingMaternities = LeaveRequest::join('users','users.id','=','leave_requests.id_user')
            //                           ->whereRaw('users.id = '.$request->id_user)
            //                           ->value('maternity_total');

            // $checkRemainingPaternities = LeaveRequest::join('users','users.id','=','leave_requests.id_user')
            //                           ->whereRaw('users.id = '.$request->id_user)
            //                           ->value('paternity_total');
                                      

            // if($daydiff > $checkRemainingHolidays){
            //     return response (['message' => 'Leave requests cannot be processed because holiday'],400);
            // } 
            
            // if ($daydiff > $checkRemainingSicks){
            //     return response (['message' => 'Leave requests cannot be processed because sick'],400);
            // } 
            
            // if ($daydiff > $checkRemainingMaternities){
            //     return response (['message' => 'Leave requests cannot be processed because maternity'],400);
            // } 
            
            // if ($daydiff > $checkRemainingPaternities){
            //     return response (['message' => 'Leave requests cannot be processed because paternity'],400);
            // }

            $data = [];
             //cek apakah ada image di file atau tidak
            if (!is_null($request->file('document'))) {
                foreach($request->file('document') as $key=>$file)
                    {
                        //$file          = $request->file('file_name');
                        $nama_file     = time() . "_" . $file->getClientOriginalName();
                        $tujuan_upload = 'document';
                        $file->move($tujuan_upload, $nama_file);
                        $data[$key] = $nama_file;
                    }
            } else {
                $nama_file = 'No Document';
            }

        $leaverequest = new LeaveRequest();
        
        $leaverequest->id_user                = $storeData['id_user'];
        $leaverequest->id_senior              = $storeData['id_senior'];
        $leaverequest->start_date             = $storeData['start_date'];
        $leaverequest->end_date               = $storeData['end_date'];
        $leaverequest->request_type           = $storeData['request_type'];
        $leaverequest->day_time               = $storeData['day_time'];
        $leaverequest->approval_status        = 0;
        $leaverequest->overall_status         = 'Up Coming';
        $leaverequest->document               = json_encode($data);
            
        $leaverequest->save();
        //$leaverequest = LeaveRequest::create($storeData);


        $leaverequest->approval_status = 0;

        $leaverequest->overall_status = 'Up Coming';

        $senior = Senior::find($leaverequest->id_senior);
        $user = User::find($leaverequest->id_user);

        try{
            $detail = [
                'name' => $user->name,
                'id_leave' => $leaverequest->id, 
            ];
            Mail::to($senior->senior_email)->send(new LeaveMail($detail));
            //Mail::to('juliyapradnyawati@gmail.com')->send(new LeaveMail($detail));
        }catch(Exception $e){
            
             //return redirect()->route('Faculty.index')->with('success','Item Created Successfully but cannot send the email');
             return response()->json(['success'=>'Send email successfully.']);
        }

        $inbox = Inbox::create([
            'id_leave_request' => $leaverequest->id,
            'unread' => 1
        ]);

        // return redirect()->route('Faculty.index')->with('success','Item Created Successfully');

        //$users = User::whereIn("id", $request->ids)->get();

        //Mail::to($user->email)->send(new UserEmail($user));

        //return response()->json(['success'=>'Send email successfully.']);
         
        return response([
            'message' => 'Add Leave Request Success',
            'data' => $leaverequest,
        ],200);
    }

    public function destroy($id){
        $leaverequest = LeaveRequest::find($id);

        if(is_null($leaverequest)){
            return response([
                'message' => 'Leave Request Not Found',
                'data' => null
            ],404);
        }

        if($leaverequest->delete()){
            return response([
                'message' => 'Delete Leave Request Success',
                'data' => $leaverequest,
            ],200);
        }
        
        return response([
            'message' => 'Delete Leave Request Failed',
            'data' => null,
        ],400);

    }

    public function update(Request $request, $id){
        $leaverequest = LeaveRequest::find($id);
        if(is_null($leaverequest)){
            return response([
                'message' => 'Leave Request Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'id_user'                   => 'required',
            'id_senior'                 => 'required',
            'start_date'                => 'required|date_format:Y-m-d',
            'end_date'                  => 'required|date_format:Y-m-d',
            'total_date'                => 'nullable',
            'request_type'              => 'required|in:Holiday,Sick,Maternity,Paternity',
            'day_time'                  => 'required|in:Fulltime,AM,PM',
            'overall_status'            => 'nullable',
            'approval_status'           => 'nullable',
            'document'                  => 'required|file|image||mimes:pdf,jpeg,png,jpg,gif,svg',

            //|file|image|mimes:jpeg,png,jpg,gif,svg
            
        ]);

        if($validate->fails())
        return response(['message' => $validate->errors()],400);

        $leaverequest->id_user                   = $updateData['id_user'];
        $leaverequest->id_senior                 = $updateData['id_senior'];
        $leaverequest->start_date                = $updateData['start_date'];
        $leaverequest->end_date                  = $updateData['end_date'];
        $leaverequest->request_type              = $updateData['request_type'];
        $leaverequest->day_time                  = $updateData['day_time'];
        $leaverequest->document                  = $updateData['document'];

        if (!is_null($request->file('document'))) {
            $file          = $request->file('document');
            $nama_file     = time() . "_" . $file->getClientOriginalName();
            $tujuan_upload = 'document';
            $file->move($tujuan_upload, $nama_file);
    
            $leaverequest->document     = $nama_file;
        }

        $leaverequest->approval_status = 0;

        $leaverequest->overall_status = 'Up Coming';
        
        if($leaverequest->save()){
            return response([
                'message' => 'Update Leave Request Success',
                'data' => $leaverequest,
            ],200);
        }

        return response([
            'message' => 'Update Leave Request Failed',
            'data' => null
        ],400);
    }

    public function updateStatusApproved(Request $request, $id){
        $leaverequest = LeaveRequest::find($id);
        if(is_null($leaverequest)){
            return response([
                'message' => 'Leave Request Not Found',
                'data' => null
            ],404);
        }

        $user = User::find($leaverequest->id_user);

        $updateData = $request->all();
        $validate = Validator::make($updateData, [
            'approval_status'           => 'nullable',
        ]);

        if($validate->fails())
        return response(['message' => $validate->errors()],400);

        $leaverequest->approval_status           = 1;

        $start_date = LeaveRequest::join('users','users.id','=','leave_requests.id_user')->whereRaw('leave_requests.id_user = '.$user->id)->value('start_date');
        $end_date = LeaveRequest::join('users','users.id','=','leave_requests.id_user')->whereRaw('leave_requests.id_user = '.$user->id)->value('end_date');
        $startdate = \Carbon\Carbon::parse($start_date);
        $enddate = \Carbon\Carbon::parse($end_date);
        $day_diff = $startdate->diffInDays($enddate);

        if($leaverequest->approval_status == 1){
            if($leaverequest->day_time  == 'Fulltime'){
                if($leaverequest->request_type == 'Holiday')
                {
                    $user->holiday_total = ($user->holiday_total - $day_diff) - 1;
                    $user->save();
                }
                else if($leaverequest->request_type == 'Sick')
                {
                    $user->sick_total = ($user->sick_total - $day_diff) - 1;
                    $user->save();
                }
                else if($leaverequest->request_type == 'Maternity')
                {
                    $user->maternity_total = ($user->maternity_total - $day_diff) - 1;
                    $user->save();
                }
                else if($leaverequest->request_type == 'Paternity')
                {
                    $user->paternity_total = ($user->paternity_total - $day_diff) - 1;
                    $user->save();
                }
            }
            
        }

        if($leaverequest->save()){
            return response([
                'message' => 'Update Leave Request Success',
                'data' => $leaverequest,
            ],200);
        }

        return response([
            'message' => 'Update Leave Request Failed',
            'data' => null
        ],400);
    }

    //harus send API di postman, ini dibuat di frontend
    public function updateOverallStatus(){
        $now = Carbon::now()->format('Y-m-d');

        $lessorGreaterDate = DB::table('leave_requests')
                                    ->select('leave_requests.start_date')
                                    ->where('leave_requests.start_date','<=',$now)
                                    ->where('leave_requests.end_date','>=',$now)
                                    ->get();
        
        $greaterEndDate = DB::table('leave_requests')
                                    ->select('leave_requests.end_date')
                                    ->where('leave_requests.end_date','<=',$now)
                                    ->get();

        if($lessorGreaterDate != null){
            $updateoverallstatus = DB::table('leave_requests')
                                   ->where('leave_requests.approval_status','=',1)
                                   ->where('leave_requests.start_date','<=',$now)
                                   ->where('leave_requests.end_date','>=',$now)
                                   ->update(['leave_requests.overall_status' => 'In Progress']);               
        } 
        
        if ($greaterEndDate != null){
            $updateoverallstatus = DB::table('leave_requests')
                                   ->where('leave_requests.approval_status','=',1)
                                   ->where('leave_requests.end_date','<=',$now)
                                   ->update(['leave_requests.overall_status' => 'Complete']);
        }

        // if(count($lessEndDate) > 0){
        //     return response([
        //         'message' => 'Retrieve All Success',
        //         'data' => $lessEndDate
        //     ],200);
        // }
                        
        return response([
            'message' => 'Update Success',
            'data' => $updateoverallstatus
        ],200);
                      
        // return response([
        //     'message' => 'Empty',
        //     'data' => null
        // ],400);
    }

}
