<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Lider;
use App\Models\Role;

class CreateMissingLideres extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lideres:create-missing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crear registros de líderes para usuarios que no los tienen';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Creando registros de líderes faltantes...');

        // Obtener rol de líder
        $liderRole = Role::where('slug', 'lider')->first();
        if (!$liderRole) {
            $this->error('No se encontró el rol "lider".');
            return 1;
        }

        // Obtener usuarios con rol líder que no tienen registro de líder
        $usuariosSinLider = User::where('role_id', $liderRole->id)
            ->whereDoesntHave('lider')
            ->get();

        if ($usuariosSinLider->isEmpty()) {
            $this->info('Todos los usuarios líderes ya tienen registro de líder.');
            return 0;
        }

        $this->info("Encontrados {$usuariosSinLider->count()} usuarios sin registro de líder:");

        foreach ($usuariosSinLider as $usuario) {
            $this->info("- {$usuario->name} ({$usuario->email})");
            
            // Crear registro de líder
            Lider::create([
                'usuario_id' => $usuario->id,
                'territorio' => 'Territorio de ' . $usuario->name,
                'descripcion_territorio' => 'Territorio asignado automáticamente',
                'meta_votos' => 100,
                'activo' => true,
            ]);
            
            $this->info("  ✓ Registro de líder creado");
        }

        $this->info("\n¡Proceso completado! Se crearon {$usuariosSinLider->count()} registros de líderes.");
        return 0;
    }
}
