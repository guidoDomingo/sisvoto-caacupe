<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barrio extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo',
        'zona_id',
        'descripcion',
        'latitud',
        'longitud',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'latitud' => 'decimal:6',
        'longitud' => 'decimal:6'
    ];

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    public function votantes()
    {
        return $this->hasMany(Votante::class, 'barrio', 'nombre');
    }

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }
}
