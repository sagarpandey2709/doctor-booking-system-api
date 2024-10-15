<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends ApiController
{
    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email',$request->email)->first();
        if(!$user || !Hash::check($request->password,$user->password)){
            return $this->errorResponse('The provided credentials are incorrect.');
        }

        $token = $user->createToken($user->role)->plainTextToken;
        return $this->successResponse(['token' => $token]);
    }

    public function Signup(Request $request){
        // Validate request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|integer|in:0,1',  // Validate role is 0 or 1
            'password' => 'required|string|min:8|confirmed',  // `confirmed` ensures password confirmation matches
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors(), 422);
        }

        // Create user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),  // Hash the password
            'role' => $request->role,  // Save role (0 or 1)
        ]);

        // Return success response
        return $this->successResponse([ 'user' => $user],'User registered successfully',201);
    }

    public function logout(Request $request)
    {
        // Revoke the user's current token
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
