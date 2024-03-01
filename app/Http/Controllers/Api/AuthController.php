<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\User;
use Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    //register
    public function register(Request $request)
    {
        $registrationData = $request->all();
        $validate = Validator::make($registrationData, [
            'id_senior'                 => 'required',
            'id_region'                 => 'required',
            'id_facility'               => 'required',
            'id_department'             => 'required',
            'name'                      => 'required',
            'email'                     => 'required',
            'password'                  => 'required',
            'title'                     => 'required',
            'employee_start_date'       => 'required',
            'holiday_total'             => 'required',
            'sick_total'                => 'required',
            'maternity_total'           => 'required',
            'paternity_total'           => 'required',
            'user_role'                 => 'required',
            'mobile_number'             => 'required',
            'personal_email'            => 'required',
            'emergency_contact_name'    => 'required',
            'emergency_contact_number'  => 'required',
            'profile_picture'           => 'nullable|file|image|mimes:jpeg,png,jpg,gif,svg',
            'address'                   => 'required',
            'bio'                       => 'required'
        ]);
   
        if($validate->fails()){
            return response(['message', $validate->errors()],400);       
        }

        $registrationData['password'] = bcrypt($request->password);
   
        $user = User::create($registrationData);

        return response([
            'message' => 'Register Succses',
            'user' => $user,
        ],200);
   }
   //login
   public function login(Request $request){
        $loginData = $request->all();
        $validate = Validator::make($loginData, [
            'email'     => 'required',
            'password'  => 'required'
        ]);

        if($validate->fails())
            return response(['message' => $validate->errors()],400);
    
        if(!Auth::attempt($loginData))
            return response(['message' => 'Invalid Credentials'],401);
    
        $user = Auth::user();
        $token = $user->createToken('Authentication Token')->accessToken;

        return response([
            'message' => 'Authenticated',
            'user' => $user,
            'token_type' => 'Bearer',
            'access_token' => $token
        ]);
    }
    //update password
     public function changePassword(Request $request) {
         if(!Hash::check($request->get('currentPassword'), Auth::user()->password)){
             return response([
                 'message' => 'Password tidak sesuai'
             ],400);
         }

         $user = User::find(auth()->user()->id);

         $user->password = bcrypt($request->newPassword);

         if($user->save()){
             return response([
                 'message' => 'password telah diubah'
             ],200);
         }

         return response([
             'message' => 'password gagal diubah'
         ],400);
     }
     //logout
    public function logout(Request $request)
    { 
        $token = $request->user()->token();
        $token->revoke();
        $response = ['message' => 'You have been successfully logged out!'];
        return response($response, 200);
    }

    public function show($id){
        $user = User::find($id); //mencari data

        if(!is_null($user)){
            return response([
                'message' => 'Retrieve user Success',
                'data' => $user
            ],200);
        }//return data customer yg ditemukan dlmbentuk json

        return response([
            'message'=>'User Not Found',
            'data' => null
        ],404);//return message customer tidak ditemukan
    }

    public function index(Request $request){
        $user = DB::table('users')
                ->join('seniors','seniors.id','=','users.id_senior')
                ->join('regions','regions.id','=','users.id_region')
                ->join('facilities','facilities.id','=','users.id_facility')
                ->join('departments','departments.id','=','users.id_department')
                ->select('users.name','users.email','users.title','users.employee_start_date',
                'users.holiday_total','users.sick_total','users.maternity_total','users.paternity_total',
                'users.user_role','users.mobile_number','users.address','users.bio','users.personal_email','users.emergency_contact_name','users.emergency_contact_number',
                'seniors.senior_name','regions.country','facilities.item','departments.department_name');
                //->paginate(request()->per_page);
                //->get();

                if ($s = $request->input('filter')) {
                    $user->whereRaw("name LIKE '%" . $s . "%'");
                        
                }

                $result = $user->paginate(request()->per_page);

        if(count($result) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $result
            ],200);
        }

        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }
    //struktur organisasi
    public function showStructure(){
        $users = DB::table('users')
                    ->join('seniors','seniors.id','=','users.id_senior')
                    ->join('regions','regions.id','=','users.id_region')
                    ->join('facilities','facilities.id','=','users.id_facility')
                    ->join('departments','departments.id','=','users.id_department')
                    ->select('users.id','users.id_senior','users.name','seniors.senior_name','regions.country','users.title','departments.department_name','users.profile_picture')
                    ->get();

        if(count($users) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $users
            ],200);
        }

        return response([
            'message' => 'Empty',
            'data' => null
        ],400);
    }
    //API Filter
    public function filterName($first_alphabet,$second_alphabet){
        $filterName = DB::table('users')
                      ->whereRaw('name regexp "^[' .$first_alphabet.'-'.$second_alphabet.']"')
                      ->pluck("name");
                      
        if(count($filterName) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $filterName
            ],200);
        }
            
        return response([
            'message'=>'Empty',
            'data' => null
        ],404);
    }
    //hapus
    public function destroy($id){
        $user = User::find($id);  

        if(is_null($user)){
            return response([
                'message' => 'User Not Found',
                'data' => null
            ],404);
        }

        if($user->delete()){
            return response([
                'message' => 'Nonaktif User Success',
                'data' => $user,
            ],200);
        }

        return response([
            'message'=>'Nonaktif User Failed',
            'data' => null,
        ],400);
    }
    //update
    public function update(Request $request, $id){
        $user = User::find($id);//cari data
        if(is_null($user)){
            return response([
                'message' => 'User Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all(); //mengambil input dr klien
        $validate = Validator::make($updateData, [
            'id_senior'                 => 'required',
            'id_region'                 => 'required',
            'id_facility'               => 'required',
            'id_department'             => 'required',
            'name'                      => 'required',
            'email'                     => 'required',
            'password'                  => 'required',
            'title'                     => 'required', 
            'employee_start_date'       => 'required',
            'holiday_total'             => 'required',
            'sick_total'                => 'required',
            'maternity_total'           => 'required',
            'paternity_total'           => 'required',
            'user_role'                 => 'required',
            'mobile_number'             => 'required',
            'personal_email'            => 'required',
            'emergency_contact_name'    => 'required',
            'emergency_contact_number'  => 'required',
            'profile_picture'           => 'nullable|file|image|mimes:jpeg,png,jpg,gif,svg',
            'address'                   => 'required',
            'bio'                       => 'required'
            
        ]);//membuat rule validasi
        
        if($validate->fails())
        return response(['message' => $validate->errors()],400);

            $user->id_senior                = $updateData['id_senior'];
            $user->id_region                = $updateData['id_region'];
            $user->id_facility              = $updateData['id_facility'];
            $user->id_department            = $updateData['id_department'];
            $user->name                     = $updateData['name'];
            $user->email                    = $updateData['email'];
            $user->title                    = $updateData['title'];
            $user->employee_start_date      = $updateData['employee_start_date'];
            $user->holiday_total            = $updateData['holiday_total'];
            $user->sick_total               = $updateData['sick_total'];
            $user->maternity_total          = $updateData['maternity_total'];
            $user->paternity_total          = $updateData['paternity_total'];
            $user->user_role                = $updateData['user_role'];
            $user->mobile_number            = $updateData['mobile_number'];
            $user->personal_email           = $updateData['personal_email'];
            $user->emergency_contact_name   = $updateData['emergency_contact_name'];
            $user->emergency_contact_number = $updateData['emergency_contact_number'];
            $user->address                  = $updateData['address'];
            $user->bio                      = $updateData['bio'];

        if($user->save()){
            return response([
                'message' => 'Update User Success',
                'data' => $user,
            ],200);
        }

        return response([
            'message' => 'Update User failed',
            'data' => null,
        ],400);
    }
    //API employee update datanya sendiri. Data dibawah ini yang hanya bisa di update sama employee
    public function updateEmployee(Request $request,$id)
    {
        $user = User::find($id);//cari data
        if(is_null($user)){
            return response([
                'message' => 'User Not Found',
                'data' => null
            ],404);
        }

        $updateData = $request->all(); //mengambil input dr klien
        $validate = Validator::make($updateData, [
            'name'                      => '', 
            'email'                     => '', 
            'mobile_number'             => '', 
            'personal_email'            => '', 
            'emergency_contact_name'    => '', 
            'emergency_contact_number'  => '', 
            'profile_picture'           => 'nullable|file|image|mimes:jpeg,png,jpg,gif,svg', 
            'address'                   => '', 
            'bio'                       => '', 
            
        ]);//membuat rule validasi
        
        if($validate->fails())
        return response(['message' => $validate->errors()],400);

        $user->name                     = $updateData['name'];
        $user->email                    = $updateData['email'];
        $user->mobile_number            = $updateData['mobile_number'];
        $user->personal_email           = $updateData['personal_email'];
        $user->emergency_contact_name   = $updateData['emergency_contact_name'];
        $user->emergency_contact_number = $updateData['emergency_contact_number'];
        $user->address                  = $updateData['address'];
        $user->bio                      = $updateData['bio'];

        if (!is_null($request->file('profile_picture'))) {
            $file          = $request->file('profile_picture');
            $nama_file     = time() . "_" . $file->getClientOriginalName();
            $tujuan_upload = 'profile_picture';
            $file->move($tujuan_upload, $nama_file);
    
            $user->profile_picture     = $nama_file;
        }
            
        if($user->save()){
            return response([
                'message' => 'Update User Success',
                'data' => $user,
            ],200);
        }

        return response([
            'message' => 'Update User failed',
            'data' => null,
        ],400);
   }

   public function generatePassword(){
        $password=Str::$user->password();
   }

}
