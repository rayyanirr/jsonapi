<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Responses\TokenResponse;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller implements HasMiddleware
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
            'name' => ['required'],
            'email' => ['required','email', 'unique:users'],
            'password' => ['required','confirmed'],
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
