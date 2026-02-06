<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero, actualizar usuarios que tengan roles que no existan más
        // Cambiar usuarios con role_id de roles que eliminaremos a role Admin (id=1)
        \DB::table('users')->whereNotNull('role_id')->update(['role_id' => null]);
        
        // Eliminar roles existentes de manera segura
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Role::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // Crear nuevos roles
        $roles = [
            [
                'nombre' => 'Admin',
                'slug' => 'admin',
                'descripcion' => 'Acceso total al sistema',
                'permisos' => [
                    'usuarios.crear',
                    'usuarios.editar',
                    'usuarios.eliminar',
                    'votantes.todos',
                    'votantes.crear',
                    'votantes.editar',
                    'votantes.eliminar',
                    'votantes.marcar_voto',
                    'lideres.gestionar',
                    'viajes.todos',
                    'viajes.crear',
                    'viajes.editar',
                    'viajes.eliminar',
                    'visitas.todas',
                    'visitas.crear',
                    'visitas.editar',
                    'visitas.eliminar',
                    'gastos.aprobar',
                    'reportes.avanzados',
                    'configuracion.sistema',
                    'auditorias.ver',
                ],
            ],
            [
                'nombre' => 'Líder',
                'slug' => 'lider',
                'descripcion' => 'Gestiona votantes, viajes y visitas sin poder marcar votos',
                'permisos' => [
                    'votantes.ver',
                    'votantes.crear',
                    'votantes.editar',
                    'votantes.propios',
                    'contactos.registrar',
                    'viajes.ver',
                    'viajes.crear',
                    'viajes.editar',
                    'viajes.solicitar',
                    'visitas.ver',
                    'visitas.crear',
                    'visitas.editar',
                    'reportes.propios',
                ],
            ],
            [
                'nombre' => 'Veedor',
                'slug' => 'veedor',
                'descripcion' => 'Acceso a votantes con opción de marcar votos',
                'permisos' => [
                    'votantes.ver',
                    'votantes.marcar_voto',
                    'contactos.ver',
                    'reportes.ver',
                ],
            ],
        ];

        foreach ($roles as $rol) {
            Role::create($rol);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restaurar roles anteriores si es necesario
        Role::truncate();
        
        $rolesAnteriores = [
            [
                'nombre' => 'Super Administrador',
                'slug' => 'superadmin',
                'descripcion' => 'Acceso total al sistema',
                'permisos' => [
                    'usuarios.crear',
                    'usuarios.editar',
                    'usuarios.eliminar',
                    'votantes.todos',
                    'lideres.gestionar',
                    'gastos.aprobar',
                    'reportes.avanzados',
                    'configuracion.sistema',
                    'auditorias.ver',
                ],
            ],
            [
                'nombre' => 'Coordinador',
                'slug' => 'coordinador',
                'descripcion' => 'Gestiona zonas y líderes',
                'permisos' => [
                    'lideres.gestionar',
                    'votantes.todos',
                    'viajes.gestionar',
                    'gastos.ver',
                    'reportes.zona',
                ],
            ],
            [
                'nombre' => 'Líder',
                'slug' => 'lider',
                'descripcion' => 'Gestiona sus votantes y voluntarios',
                'permisos' => [
                    'votantes.propios',
                    'votantes.asignar',
                    'contactos.registrar',
                    'viajes.solicitar',
                    'reportes.propios',
                ],
            ],
            [
                'nombre' => 'Voluntario',
                'slug' => 'voluntario',
                'descripcion' => 'Registra votantes y contactos',
                'permisos' => [
                    'votantes.crear',
                    'contactos.registrar',
                    'votantes.ver',
                ],
            ],
            [
                'nombre' => 'Auditor',
                'slug' => 'auditor',
                'descripcion' => 'Solo lectura de todo el sistema',
                'permisos' => [
                    'votantes.ver',
                    'gastos.ver',
                    'reportes.ver',
                    'auditorias.ver',
                ],
            ],
        ];

        foreach ($rolesAnteriores as $rol) {
            Role::create($rol);
        }
    }
};
