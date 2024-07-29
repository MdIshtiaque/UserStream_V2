<?php

namespace App\Http\Controllers;

use App\Events\UpdateDataset;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $token = JWTAuth::fromUser($user);
            $data = [
                'user' => $user,
                'token' => $token,
            ];
            // Broadcast the new user using UpdateDataset event
            broadcast(new UpdateDataset([$user], "single"));
        }catch (Exception $exception) {
            return sendErrorResponse('Something went wrong: '.$exception->getMessage());
        }
        return sendSuccessResponse('User Created Successfully', 201, $data);
    }

    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        try {
            $credentials = $request->only('email', 'password');

            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        }catch (Exception $exception) {
            return sendErrorResponse('Something went wrong: '.$exception->getMessage());
        }
        return sendSuccessResponse('User Login Successfully', 200, $token);
    }

    public function logout(): JsonResponse
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

        } catch (JWTException $exception) {
            return sendErrorResponse('Something went wrong: '.$exception->getMessage(), 500);
        }
        return sendSuccessResponse('User Logged Out Successfully', 200);
    }
}
