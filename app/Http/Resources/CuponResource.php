<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\jsonApiResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CuponResource extends JsonResource
{
    use jsonApiResource;

    public function toJsonApi(): array
    {
        return [
            'name' => $this->resource->NOMBRE,
            'code' => $this->resource->CODIGO,
            'cod-prod' => $this->resource->PRODNUM,
            'type' => $this->resource->TIPO,
            'status' => $this->resource->STATUS,
            'activation-date' => $this->resource->FECHA_ACTIVACION,
            'expiration-date' => $this->resource->FECHA_EXPIRACION,
            'result' => $this->resource->RESULTADO,
            'email' => $this->resource->CORREO,
            'created-at' => $this->resource->FECHA_CREACION,
            'status_email' => $this->resource->STATUS_ENVIO_CORREO,
            'ip-creation' => $this->resource->IP_CREACION,
            'name-equipment' => $this->resource->NOMBRE_EQUIPO,
            'type-courtesy' => $this->resource->TIPO_CORTESIA,
            'employee-id' => $this->resource->CEDULA_EMPLEADO

        ];
    }
}
