<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\Response;

class ValidateJsonApiDocument
{
    public function handle(Request $request, Closure $next): Response
    {

        if ($request->isMethod('POST') || $request->isMethod('PATCH')) {

            $request->validate([
                'data' => ['required', 'array'],
                'data.type' => ['required', 'string'],
                'data.attributes' => [
                    Rule::requiredIF(
                        ! Str::of(request()->url())->contains('relationships')
                    ),
                    'array',

                ],
            ]);
        }

        if ($request->isMethod('PATCH')) {
            $request->validate([
                'data.id' => ['required', 'string'],

            ]);
        }

        return $next($request);
    }
}
