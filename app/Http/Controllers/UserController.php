<?php

namespace App\Http\Controllers;

use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function register(Request $request){
        //Validation
        $data = Validator::make($request->all(), [
            'name' => ['nullable'],
            'login' => ['required', 'unique:users', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::default()],
        ]);

        //Checking for errors
        if ($data->fails()){
            return response()->json([
                'error' => $data->errors()
            ]);
        }

        //Object to array conversion
        $data = $data->validate();

        //Password Encryption
        $data['password'] = Hash::make($data['password']);

        //Database insertion
        $user = User::create($data);

        return response()->json([
            'user'=> $user,
            'message' => "User added"
        ]);
    }

    public function login(LoginRequest $request){
        if(!Auth::attempt($request->only(['email', 'password']))){
            return response()->json([
                'message' => 'Email and Password not matching',
            ]);
        }

        $user = User::where('email', $request->email)->first();
        Auth::login($user );

        return response()->json([
            'message' => 'Logged in',
            'user'=> Auth::user()
        ]);
    }
}
