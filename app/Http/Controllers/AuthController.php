<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Token;
use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;
//use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
//use Illuminate\Foundation\Auth\RegistersUsers;
use Auth;

class AuthController extends Controller
{
//    public function __construct()
//    {
//        dd('__');
//        $this->middleware('guest');
//    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function Create ( Request $request )
    {
        dump(count($request->all()));
        if (count($request->all()) == 0){
            return response()->json(['message' => 'No data']);
        };
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);
        $errors = $validator->errors()->first();
        if($errors != ''){
            return response()->json(['$errors' => $errors],403);
        }
         $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => $request->get('password'),
        ]);
        $user->save();
        return response()->json(['message' => 'Created user'], 201);
    }

     protected function Autorization ( Request $request ){


         $validator = Validator::make($request->all(), [
             'email' => 'required|string|email|max:255',
             'password' => 'required|string|min:6',
         ]);
         $errors = $validator->errors()->first();
         if($errors != ''){
             return response()->json(['errors' => $errors],403);
         }

         $user = User::where('email', $request->get('email'))->get();
         $count = count($user);
         if($count == 0){
             return response()->json(["message" => 'Unautorise']);
         };
         if($user[0]['password'] != $request->get('password')){
             return response()->json(["message" => 'Wrong password']);
         }
         $bytes = random_bytes(66);
         $token = bin2hex($bytes);
         Token::create([
             'token' => $token
         ]);
         return response()->json([
             'message'=> 'Autorize',
             'token' => $token]);
     }
}
