<?php

namespace App\Models\Kfc;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cupon extends Model
{
    use HasFactory;

    protected $connection = 'cortesia';
    protected $table = 'CORTESIAS';
    public $timestamps = false;
    protected $primaryKey = 'ID';
    public $resourceType = 'cupons';

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Asignar valores predeterminados
            $defaults = [
                'FECHA_CREACION' => date('Y-m-d'),
                'STATUS' => 0,
                'IP_CREACION' => request()->user()->name,
                'NOMBRE_EQUIPO' => gethostname()

            ];

            foreach ($defaults as $key => $value) {
                if (!isset($model->$key)) {
                    $model->$key = $value;
                }
            }
        });
    }

    protected $fillable = [
        'id',
        'name',
        'code',
        'cod-prod',
        'type',
        'status',
        'activation-date',
        'expiration-date',
        'result',
        'email',
        'created-at',
        'status_email',
        'ip-creation',
        'name-equipment',
        'type-courtesy',
        'employee-id'

    ];

    protected $fieldMap = [
        'id' => 'ID',
        'name' => 'NOMBRE',
        'code' => 'CODIGO',
        'cod-prod' => 'PRODNUM',
        'type' => 'TIPO',
        'status' => 'STATUS',
        'activation-date' => 'FECHA_ACTIVACION',
        'expiration-date' => 'FECHA_EXPIRACION',
        'result' => 'RESULTADO',
        'email' => 'CORREO',
        'created-at' => 'FECHA_CREACION',
        'status_email' => 'STATUS_ENVIO_CORREO',
        'ip-creation' => 'IP_CREACION',
        'name-equipment' => 'NOMBRE_EQUIPO',
        'type-courtesy' => 'TIPO_CORTESIA',
        'employee-id' => 'CEDULA_EMPLEADO'
    ];

    public function getAttribute($key)
    {
        $key = $this->fieldMap[$key] ?? $key;

        return parent::getAttribute($key);
    }

    public function setAttribute($key, $value)
    {
        $key = $this->fieldMap[$key] ?? $key;

        return parent::setAttribute($key, $value);
    }

    public function scopeWhereMapped($query, $attribute, $value)
    {
        $columnName = $this->fieldMap[$attribute] ?? $attribute;

        return $query->where($columnName, $value);
    }
}
