<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Distrito extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo',
        'departamento',
        'descripcion',
        'poblacion_estimada',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'poblacion_estimada' => 'integer'
    ];

    public function zonas()
    {
        return $this->hasMany(Zona::class);
    }

    public function votantes()
    {
        return $this->hasMany(Votante::class, 'distrito', 'nombre');
    }

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }
}
