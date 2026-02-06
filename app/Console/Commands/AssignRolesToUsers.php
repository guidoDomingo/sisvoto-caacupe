<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Role;

class AssignRolesToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:assign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Asignar roles a usuarios existentes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando asignación de roles...');

        // Obtener roles
        $adminRole = Role::where('slug', 'admin')->first();
        $liderRole = Role::where('slug', 'lider')->first();
        $veedorRole = Role::where('slug', 'veedor')->first();

        if (!$adminRole || !$liderRole || !$veedorRole) {
            $this->error('No se encontraron todos los roles necesarios.');
            return 1;
        }

        // Asignar rol admin al primer usuario o crear uno
        $adminUser = User::first();
        if ($adminUser) {
            $adminUser->role_id = $adminRole->id;
            $adminUser->save();
            $this->info("Usuario {$adminUser->name} asignado como Admin.");
        } else {
            $this->warn('No hay usuarios en el sistema. Crea un usuario primero.');
        }

        // Asignar roles a usuarios con líderes
        $usuariosConLider = User::whereHas('lider')->get();
        foreach ($usuariosConLider as $user) {
            $user->role_id = $liderRole->id;
            $user->save();
            $this->info("Usuario {$user->name} asignado como Líder.");
        }

        // Mostrar opciones para asignar veedores
        $usuariosSinRol = User::whereNull('role_id')->get();
        if ($usuariosSinRol->count() > 0) {
            $this->info("\nUsuarios sin rol asignado:");
            foreach ($usuariosSinRol as $user) {
                $opcion = $this->choice(
                    "¿Qué rol quieres asignar a {$user->name} ({$user->email})?",
                    ['Admin', 'Líder', 'Veedor', 'Saltar'],
                    'Veedor'
                );

                switch ($opcion) {
                    case 'Admin':
                        $user->role_id = $adminRole->id;
                        break;
                    case 'Líder':
                        $user->role_id = $liderRole->id;
                        break;
                    case 'Veedor':
                        $user->role_id = $veedorRole->id;
                        break;
                    case 'Saltar':
                        continue 2;
                }

                $user->save();
                $this->info("Usuario {$user->name} asignado como {$opcion}.");
            }
        }

        $this->info("\n¡Asignación de roles completada!");
        return 0;
    }
}
