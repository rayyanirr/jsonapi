<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Responses\TokenResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class LoginController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            (new Middleware('guest:sanctum'))
        ];
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'device_name' => ['required'],

        ]);

        $user = User::whereEmail($request->email)->first();

        if (!$user || ! Hash::check($request->password, $user->password)) {
           throw  ValidationException::withMessages([
                'email' => [__('auth.failed')]
           ]);

        }

        return new TokenResponse($user);
    }
}
