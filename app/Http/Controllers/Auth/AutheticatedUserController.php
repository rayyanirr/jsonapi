<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AutheticatedUserController extends Controller
{
    public function __invoke(Request $request)
    {
        return $request->user();
    }
}
