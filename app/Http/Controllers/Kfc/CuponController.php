<?php

namespace App\Http\Controllers\Kfc;

use App\Http\Controllers\Controller;
use App\Http\Resources\CuponResource;
use App\Models\Kfc\Cupon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class CuponController extends Controller
{
    public static function middleware()
    {
        return [
            (new Middleware('auth:sanctum'))->only(['index','store', 'update', 'destroy']),
        ];
    }

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Cupon::class);

        $cupons = Cupon::query()
            //->allowedIncludes(['category', 'author', 'comments'])
            ->allowedFilters(['name', 'code', 'email', 'status', 'created-at', 'expiration-date'])
            ->allowedSorts(['name', 'status', 'created-at','expiration-date'])
            ->sparseFieldset()
            ->jsonPaginate();

        return CuponResource::collection($cupons);
    }

    public function show($cupon): JsonResource
    {
        $cupon = Cupon::whereMapped('code', "=" ,$cupon)
            ->sparseFieldset()
            ->firstOrFail();

        return CuponResource::make($cupon);
    }



}
