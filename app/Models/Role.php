<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = 'roles';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'permisos',
    ];

    protected $casts = [
        'permisos' => 'array',
    ];

    /**
     * Relación con usuarios
     */
    public function usuarios()
    {
        return $this->hasMany(User::class, 'role_id');
    }

    /**
     * Verificar si el rol tiene un permiso específico
     */
    public function tienePermiso(string $permiso): bool
    {
        if (!$this->permisos) {
            return false;
        }

        // Asegurar que permisos sea un array
        $permisos = $this->permisos;
        if (is_string($permisos)) {
            $permisos = json_decode($permisos, true) ?: [];
        }
        
        if (!is_array($permisos)) {
            return false;
        }

        return in_array($permiso, $permisos);
    }
}
