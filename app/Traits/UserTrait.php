<?php

namespace App\Traits;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

trait UserTrait
{

    public function list()
    {
        try {
            $users = $this->userRepo->list([]);
            return api_response($users, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function delete($pid)
    {
        try {
            $data = $this->userRepo->searchByPid($pid);
            if (!$data) {
                return api_response([], false, "Invalid data. User ID not found", code: 404);
            }
            $data = $this->userRepo->delete($data);
            return api_response([], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();
            if (Auth::attempt($credentials)) {
                $user = Auth::user(); // get authenticated user
                $tokenInstance = $user->createToken('auth_token');
                $tokenInstance->accessToken->expires_at = now()->addDays(config('app.token_expiry'));
                $tokenInstance->accessToken->save();
                $token = $tokenInstance->plainTextToken;
                return api_response(["token" => $token], true, "Success", 200);
            }
            return api_response([], false,  "Invalid credentials.", 401);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function register(RegisterRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = User::create([
                'email' => $validated["email"],
                'firstname' => $validated["firstname"],
                'lastname' => $validated["lastname"],
                'middlename' => $validated["middlename"],
                'suffix' => $validated["suffix"],
                'date_of_birth' => $validated["date_of_birth"],
                'license_no' => $validated["license_no"],
                'gender' => $validated["gender"],
                'password' => Hash::make($validated["password"])
            ]);
            return api_response(["user" => $user], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
