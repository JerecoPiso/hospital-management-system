<?php

namespace App\Traits;

use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;

trait UserTrait
{
    public function list(Request $request)
    {
        try {
            $users = $this->userRepo->list($request->only(['search', 'per_page', 'page']));
            return api_list_response($users['items'], $users['meta']);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
    public function view($user_id)
    {
        try {
            $user = $this->userRepo->searchByPid($user_id);
            if (!$user) {
                return api_response([], false, "User not found", 404);
            }
            return api_response($user, true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function update($order_pid, UserUpdateRequest $request)
    {
        try {
            $validated = $request->validated();
            if (empty($validated['password'])) {
                unset($validated['password']);
            }
            $validated["date_of_birth"] = Carbon::parse($validated["date_of_birth"])->format('Y-m-d');
            $user = $this->userRepo->searchByPid($order_pid);
            if (!$user) {
                return api_response([], false, "User not found", 404);
            }
            $user = $this->userRepo->update($user->id, $validated);
            if (!$user) {
                return api_response([], false, "User not updated", 500);
            }
            return api_response(["user" => $user], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $credentials = $request->validated();
            if (Auth::attempt($credentials)) {
                $request->session()->regenerate(); // Prevent session fixation
                return api_response(
                    [
                        "user" => Auth::user(),
                        "session_id" => session()->getId(),        // 👈 current session ID
                        "session"    => session()->all(),          // 👈 all session data
                    ],
                    true,
                    "Success",
                    200
                );
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
            $user = $this->userRepo->create([
                'email' => $validated["email"],
                'firstname' => $validated["firstname"],
                'lastname' => $validated["lastname"],
                'middlename' => $validated["middlename"],
                'suffix' => $validated["suffix"],
                'date_of_birth' => Carbon::parse($validated["date_of_birth"])->format('Y-m-d'),
                'license_no' => $validated["license_no"],
                'gender' => $validated["gender"],
                'password' => Hash::make($validated["password"]),
                'role_pid' => $validated["role_pid"] ?? null,
            ]);
            return api_response(["user" => $user], true, "Success", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = $request->user();

            if (!Hash::check($validated['current_password'], $user->password)) {
                return api_response([], false, "Current password is incorrect", 422);
            }

            $this->userRepo->update($user->id, ['password' => $validated['password']]);

            return api_response([], true, "Password changed successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }

    public function me(Request $request)
    {
        $user = $request->user()?->load('role');

        return api_response([
            'user' => $user,
            'permissions' => $user?->permissionsMap() ?? [],
        ], true, "Success", 200);
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

    public function logout(Request $request)
    {
        try {
            Auth::guard('web')->logout();           // Log the user out
            $request->session()->invalidate();       // Invalidate the session
            $request->session()->regenerateToken();  // Regenerate CSRF token
            return api_response([], true, "Logged out successfully", 200);
        } catch (\Exception $e) {
            return api_response([], false,  $e->getMessage(), $code = $e->getCode() ?: 500);
        }
    }
}
