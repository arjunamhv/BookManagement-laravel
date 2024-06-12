<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Resources\UserResource;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        if(User::where('username', $data['username'])->count() == 1) {
            throw new HttpResponseException(response(['errors' => ['username' => ['Username already exists']]], 422));
        }

        $data['password'] = Hash::make($data['password']);

        $user = new User($data);
        $user->save();
        return (new userResource($user))->response()->setStatusCode(201);
    }

    public function login(UserLoginRequest $request): UserResource
    {
        $data = $request->validated();
        $user = User::where('username', $data['username'])->first();
        if(!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response(['errors' => ['message' => ['Invalid username or password']]], 401));
        }

        $user->token = Str::uuid()->toString();
        $user->save();
        return new UserResource($user);
    }

    public function get(): UserResource
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();
        $user = User::find($user->id);
        if(isset($data['password'])){
             $password = Hash::make($data['password']);
             $user->password = $password;
            $user->save();
            return response()->json(['message' => 'password updated'])->setStatusCode(200);
        } else {
            return response()->json(['message' => 'nothing change'])->setStatusCode(200);
        }
    }

    public function logout(): JsonResponse
    {
        $user = Auth::user();
        $user = User::find($user->id);
        $user->token = null;
        $user->save();
        return response()->json([
            'data' => 'true'
        ])->setStatusCode(200);
    }
}
