<?php

namespace App\Http\Controllers\Api;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogoutController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            (new Middleware('auth:sanctum'))
        ];
    }


    public function __invoke(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->noContent();
    }
}
