<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Hash;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Auth endpoints
 */
class LoginController extends Controller
{
    /**
     * POST Login
     *
     * Login with the existing user.
     *
     * @response {"access_token":"1|a9ZcYzIrLURVGx6Xe41HKj1CrNsxRxe4pLA2oISo"}
     * @response 422 {"error": "The provided credentials are incorrect."}
     */
    public function __invoke(Request $request): JsonResponse
    {
        $user = User::query()->where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'error' => 'The provided credentials are incorrect.',
            ], 422);
        }

        $device = substr($request->userAgent() ?? '', 0, 255);

        return response()->json([
            'access_token' => $user->createToken($device)->plainTextToken,
        ]);
    }
}
