<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
    Route::post('register', 'Api\AuthController@register');
    Route::post('login', 'Api\AuthController@login');
    
    Route::get('user', 'Api\AuthController@index');
    Route::get('user/{id}', 'Api\AuthController@show');
    Route::put('user/{id}', 'Api\AuthController@update');
    Route::delete('user/{id}', 'Api\AuthController@destroy');
    Route::post('updateemployee/{id}', 'Api\AuthController@updateEmployee');
    Route::put('changepassword', 'Api\AuthController@changePassword');
    Route::get('showstructure', 'Api\AuthController@showStructure');
    Route::get('filtername/{first_alphabet},{second_alphabet}', 'Api\AuthController@filterName');
    Route::get('generatepassword', 'Api\AuthController@generatePassword');

    Route::get('employee/{id}', 'Api\EmployeeController@show');
    Route::post('employee', 'Api\EmployeeController@store');
    Route::put('employee/{id}', 'Api\EmployeeController@update');
    Route::delete('employee/{id}', 'Api\EmployeeController@destroy');
    Route::get('employee', 'Api\EmployeeController@index');
    

    Route::get('holiday/{id}', 'Api\HolidayController@show');
    Route::post('holiday', 'Api\HolidayController@store');
    Route::put('holiday/{id}', 'Api\HolidayController@update');
    Route::delete('holiday/{id}', 'Api\HolidayController@destroy');
    Route::get('holiday', 'Api\HolidayController@index');

    Route::get('leaverequest/{id}', 'Api\LeaveRequestController@show');
    Route::post('leaverequest', 'Api\LeaveRequestController@store');
    Route::post('leaverequest/{id}', 'Api\LeaveRequestController@update');
    Route::delete('leaverequest/{id}', 'Api\LeaveRequestController@destroy');
    Route::get('leaverequest', 'Api\LeaveRequestController@index');
    Route::put('updatestatusapproved/{id}', 'Api\LeaveRequestController@updateStatusApproved');
    Route::get('updateoverallstatus', 'Api\LeaveRequestController@updateOverallStatus');

    Route::get('senior/{id}', 'Api\SeniorController@show');
    Route::post('senior', 'Api\SeniorController@store');
    Route::put('senior/{id}', 'Api\SeniorController@update');
    Route::delete('senior/{id}', 'Api\SeniorController@destroy');
    Route::get('senior', 'Api\SeniorController@index');

    Route::get('department/{id}', 'Api\DepartmentController@show');
    Route::post('department', 'Api\DepartmentController@store');
    Route::put('department/{id}', 'Api\DepartmentController@update');
    Route::delete('department/{id}', 'Api\DepartmentController@destroy');
    Route::get('department', 'Api\DepartmentController@index');

    Route::get('position/{id}', 'Api\PositionController@show');
    Route::post('position', 'Api\PositionController@store');
    Route::put('position/{id}', 'Api\PositionController@update');
    Route::delete('position/{id}', 'Api\PositionController@destroy');
    Route::get('position', 'Api\PositionController@index');

    Route::get('media/{id}', 'Api\MediaController@show');
    Route::post('media', 'Api\MediaController@store');
    Route::post('media/{id}', 'Api\MediaController@update');
    Route::delete('media/{id}', 'Api\MediaController@destroy');
    Route::get('media', 'Api\MediaController@index');

    Route::get('region/{id}', 'Api\RegionController@show');
    Route::post('region', 'Api\RegionController@store');
    Route::put('region/{id}', 'Api\RegionController@update');
    Route::delete('region/{id}', 'Api\RegionController@destroy');
    Route::get('region', 'Api\RegionController@index');

    Route::get('facility/{id}', 'Api\FacilityController@show');
    Route::post('facility', 'Api\FacilityController@store');
    Route::put('facility/{id}', 'Api\FacilityController@update');
    Route::delete('facility/{id}', 'Api\FacilityController@destroy');
    Route::get('facility', 'Api\FacilityController@index');

    Route::get('setting/{id}', 'Api\SettingController@show');
    Route::post('setting', 'Api\SettingController@store');
    Route::post('setting/{id}', 'Api\SettingController@update');
    Route::delete('setting/{id}', 'Api\SettingController@destroy');
    Route::get('setting', 'Api\SettingController@index');

    Route::get('icon/{id}', 'Api\IconController@show');
    Route::post('icon', 'Api\IconController@store');
    Route::put('icon/{id}', 'Api\IconController@update');
    Route::delete('icon/{id}', 'Api\IconController@destroy');
    Route::get('icon', 'Api\IconController@index');

    Route::post('inbox', 'Api\InboxController@store');
    Route::delete('inbox/{id}', 'Api\InboxController@destroy');
    Route::get('inbox', 'Api\InboxController@index');
    Route::put('inbox/{id}', 'Api\InboxController@update');

    Route::group(['middleware' => 'auth:api'], function(){
    
    Route::post('logout', 'Api\AuthController@logout');

});

