<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Zona extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'codigo',
        'distrito_id',
        'descripcion',
        'color',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    public function distrito()
    {
        return $this->belongsTo(Distrito::class);
    }

    public function barrios()
    {
        return $this->hasMany(Barrio::class);
    }

    public function votantes()
    {
        return $this->hasMany(Votante::class, 'zona', 'nombre');
    }

    public function scopeActivo($query)
    {
        return $query->where('activo', true);
    }
}
