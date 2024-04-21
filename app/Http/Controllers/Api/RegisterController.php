<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Responses\TokenResponse;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class RegisterController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            (new Middleware('guest:sanctum')),
        ];
    }

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {

        $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'confirmed'],
            'device_name' => ['required'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        return new TokenResponse($user);
    }
}
