<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Product\UserRequest;
use App\Http\Resources\User\UserResource;
use App\Traits\Api\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function auth(UserRequest $request)
    {
        $data = $request->validated();
        if (!Auth::attempt($data)) return $this->responseError(msg: "credential not allowed");

        $user = Auth::user();

        $token = $user->createToken('api-token')->plainTextToken;

        return $this->responseData(["user" => new UserResource($user), "token" => $token]);
    }
}
