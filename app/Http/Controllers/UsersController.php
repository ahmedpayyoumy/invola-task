<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

class UsersController extends Controller
{
    // Registration Function
    public function Register(Request $request)
    {
        // Validation Rules
        $validator = Validator::make(
            $request->all(),
            [
                'name' => ['required'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'confirmed', Password::min(8)],
                'is_vendor' => ['required', 'in:0,1'],
                'phone' => ['required', 'regex:/((10)|(11)|(12)|(15)|(010)|(011)|(012)|(015))[0-9]{8}/'],
                'address' => ['required']
            ],
            [
                'phone.regex' => 'the input Phone must be Egyptian number'
            ]
        );

        // if validator fails
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please Fix errors.',
                'errors' => $validator->errors()
            ]);
        }

        // if there is no validation errors
        User::create([
            'name'      => $request->name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
            'phone'     => $request->phone,
            'address'   => $request->address,
            'is_vendor' => $request->is_vendor
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User registered succesfully, Please Login.'
        ], 200);
    }

    // Login Function
    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => ['required', 'email'],
                'password' => ['required', Password::min(8)]
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Wrong Information Passed.',
                'errors' => $validator->errors()
            ]);
        }

        // authentication attempt
        if (auth()->attempt($request->only(['email', 'password']))) {
            $token = auth()->user()->createToken('passport_token')->accessToken;

            return response()->json([
                'success' => true,
                'message' => 'User login succesfully, Use token to authenticate.',
                'token' => $token,
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'User authentication failed.'
            ], 401);
        }
    }

}
