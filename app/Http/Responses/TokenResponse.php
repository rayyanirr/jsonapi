<?php

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Support\Facades\Auth;

class TokenResponse implements Responsable
{
    private User $user;

    public function __construct(User $user)
    {

        $this->user = $user;
    }

    public function toResponse($request)
    {

        $plainTextToken = $this->user->createToken(
            $request->device_name,
            $this->user->getAllPermissions()->pluck('name')->toArray()
        )->plainTextToken;

        return response()->json([
            'plain-text-token' => $plainTextToken,
        ]);
    }
}
