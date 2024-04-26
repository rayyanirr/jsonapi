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
                'data.type' => [
                    'required_without:data.0.type',
                    'string'
                ],
                'data.attributes' => [
                    Rule::requiredIF(
                        ! Str::of(request()->url())->contains('relationships')
                        && request()->isNotFilled('data.0.type')
                    ),
                    'array',

                ],
            ]);
        }

        if ($request->isMethod('PATCH')) {
            $request->validate([
                'data.id' => [
                    'required_without:data.0.id',
                    'string'],

            ]);
        }

        return $next($request);
    }
}
