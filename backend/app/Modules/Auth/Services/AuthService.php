<?php

namespace App\Modules\Auth\Services;

use App\Modules\Auth\DTOs\RegisterDTO;
use App\Modules\Auth\Events\PasswordReset;
use App\Modules\Auth\Events\UserLoggedIn;
use App\Modules\Auth\Events\UserLoggedOut;
use App\Modules\Auth\Events\UserRegistered;
use App\Modules\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset as LaravelPasswordReset;
use Exception;

class AuthService
{
    public function register(RegisterDTO $dto): array
    {
        try {
            return DB::transaction(function () use ($dto) {
                $user = User::create([
                    'name' => $dto->name,
                    'email' => $dto->email,
                    'password' => Hash::make($dto->password),
                    'tenant_id' => $dto->tenant_id,
                    'status' => 'active',
                ]);

                $token = $user->createToken('auth_token')->plainTextToken;

                event(new UserRegistered($user));

                return [
                    'user' => $user->load(['tenant', 'roles', 'permissions']),
                    'token' => $token,
                ];
            });
        } catch (Exception $e) {
            throw new Exception('Registration failed: ' . $e->getMessage());
        }
    }

    public function login(string $email, string $password): array
    {
        try {
            $user = User::where('email', $email)->first();

            if (!$user || !Hash::check($password, $user->password)) {
                throw new Exception('Invalid credentials');
            }

            if (!$user->isActive()) {
                throw new Exception('Account is inactive');
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            event(new UserLoggedIn($user));

            return [
                'user' => $user->load(['tenant', 'roles', 'permissions']),
                'token' => $token,
            ];
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function logout(User $user): bool
    {
        try {
            $user->currentAccessToken()->delete();

            event(new UserLoggedOut($user));

            return true;
        } catch (Exception $e) {
            throw new Exception('Logout failed: ' . $e->getMessage());
        }
    }

    public function forgotPassword(string $email): bool
    {
        try {
            $user = User::where('email', $email)->first();

            if (!$user) {
                throw new Exception('User not found');
            }

            $status = Password::sendResetLink(['email' => $email]);

            if ($status !== Password::RESET_LINK_SENT) {
                throw new Exception('Failed to send password reset link');
            }

            return true;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function resetPassword(string $email, string $token, string $password): bool
    {
        try {
            return DB::transaction(function () use ($email, $token, $password) {
                $status = Password::reset(
                    [
                        'email' => $email,
                        'password' => $password,
                        'password_confirmation' => $password,
                        'token' => $token,
                    ],
                    function (User $user, string $password) {
                        $user->forceFill([
                            'password' => Hash::make($password),
                        ])->save();

                        $user->tokens()->delete();

                        event(new LaravelPasswordReset($user));
                        event(new PasswordReset($user));
                    }
                );

                if ($status !== Password::PASSWORD_RESET) {
                    throw new Exception('Failed to reset password');
                }

                return true;
            });
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function updateProfile(User $user, array $data): bool
    {
        try {
            return DB::transaction(function () use ($user, $data) {
                $updateData = [];

                if (isset($data['name'])) {
                    $updateData['name'] = $data['name'];
                }

                if (isset($data['email'])) {
                    $updateData['email'] = $data['email'];
                }

                if (isset($data['phone'])) {
                    $updateData['phone'] = $data['phone'];
                }

                $user->update($updateData);

                return true;
            });
        } catch (Exception $e) {
            throw new Exception('Profile update failed: ' . $e->getMessage());
        }
    }

    public function updatePassword(User $user, string $currentPassword, string $newPassword): bool
    {
        try {
            return DB::transaction(function () use ($user, $currentPassword, $newPassword) {
                if (!Hash::check($currentPassword, $user->password)) {
                    throw new Exception('Current password is incorrect');
                }

                $user->update([
                    'password' => Hash::make($newPassword),
                ]);

                $user->tokens()->delete();

                return true;
            });
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
