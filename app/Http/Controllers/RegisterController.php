<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegisterController extends Controller
{
    //

    protected function registered(Request $request, $user){
      
      $user->country_code = $request->country_code;
      $user->phone = $request->phone;
      $user->save();

    }

    protected function validator(array $data){
    	return Validator::make($data,[
         'name'     => 'required|max:255',
         'email'    => 'required|email|max:255|unique:users',
         'password' => 'required|min:6|confirmed',
         'country_code' => 'required',
         'phone' => 'required'

    	]);
    }
}
