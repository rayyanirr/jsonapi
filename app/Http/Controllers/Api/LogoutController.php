<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class LogoutController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            (new Middleware('auth:sanctum')),
        ];
    }

    public function __invoke(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->noContent();
    }
}
