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

    protected $fillable = [];

    protected $fieldMap = [
        'ID' => 'id',
        'NOMBRE' => 'name',
        'CODIGO' => 'code',
        'PRODNUM' => 'cod-prod',
        'TIPO' => 'type',
        'STATUS' => 'status',
        'FECHA_ACTIVACION' => 'activation-date',
        'FECHA_EXPIRACION' => 'expiration-date',
        'RESULTADO' => 'result',
        'CORREO' => 'email',
        'FECHA_CREACION' => 'created-at',
        'STATUS_ENVIO_CORREO' => 'status_email',
        'IP_CREACION' => 'ip-creation',
        'NOMBRE_EQUIPO' => 'name-equipment',
        'TIPO_CORTESIA' => 'type-courtesy',
        'CEDULA_EMPLEADO' => 'employee-id'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        // Crear dinámicamente los métodos de acceso y mutadores
        foreach ($this->fieldMap as $original => $mapped) {
            $this->addDynamicMethod($original, $mapped);
        }
    }

    protected function addDynamicMethod($original, $mapped)
    {
        $this->addGetter($original, $mapped);
        $this->addSetter($original, $mapped);
    }

    protected function addGetter($original, $mapped)
    {
        $this->{"get{$mapped}Attribute"} = function () use ($original) {
            return $this->attributes[$original];
        };
    }

    protected function addSetter($original, $mapped)
    {
        $this->{"set{$mapped}Attribute"} = function ($value) use ($original) {
            $this->attributes[$original] = $value;
        };
    }

    public function getFieldMap()
    {
        return $this->fieldMap;
    }

    public function scopeWhereMapped($query, $field, $operator, $value)
{
    // Obtén el mapeo de los campos del modelo actual
    $fieldMap = $this->getFieldMap();

    // Convertir el nombre del campo mapeado al nombre original del campo
    $originalField = array_search($field, $fieldMap);

    return $query->where($originalField, $operator, $value);
}
}
