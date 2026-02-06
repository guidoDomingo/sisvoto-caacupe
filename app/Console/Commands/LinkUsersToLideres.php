<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Lider;
use App\Models\Role;

class LinkUsersToLideres extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:link-lideres';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Vincular usuarios con rol Líder a registros de líderes existentes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando vinculación de usuarios con líderes...');

        // Obtener rol de líder
        $liderRole = Role::where('slug', 'lider')->first();
        if (!$liderRole) {
            $this->error('No se encontró el rol "lider".');
            return 1;
        }

        // Obtener usuarios con rol líder
        $usuariosLider = User::where('role_id', $liderRole->id)->whereDoesntHave('lider')->get();
        $this->info("Encontrados {$usuariosLider->count()} usuarios líder sin registro de líder asociado.");

        // Obtener líderes sin usuario asociado
        $lideresSinUsuario = Lider::whereDoesntHave('usuario')->get();
        $this->info("Encontrados {$lideresSinUsuario->count()} líderes sin usuario asociado.");

        if ($usuariosLider->isEmpty() || $lideresSinUsuario->isEmpty()) {
            $this->info('No hay usuarios o líderes para vincular.');
            return 0;
        }

        // Vincular automáticamente por nombre o permitir selección manual
        foreach ($usuariosLider as $usuario) {
            // Buscar líder por nombre similar
            $liderEncontrado = $lideresSinUsuario->filter(function ($lider) use ($usuario) {
                return stripos($lider->nombres . ' ' . $lider->apellidos, explode(' ', $usuario->name)[0]) !== false;
            })->first();

            if ($liderEncontrado) {
                $liderEncontrado->usuario_id = $usuario->id;
                $liderEncontrado->save();
                $this->info("✓ Usuario '{$usuario->name}' vinculado automáticamente con líder '{$liderEncontrado->nombres} {$liderEncontrado->apellidos}'");
                
                // Remover de la colección para no reutilizar
                $lideresSinUsuario = $lideresSinUsuario->reject(function ($lider) use ($liderEncontrado) {
                    return $lider->id === $liderEncontrado->id;
                });
            } else {
                // Selección manual
                if ($lideresSinUsuario->isNotEmpty()) {
                    $this->info("\nUsuario sin vinculación automática: {$usuario->name} ({$usuario->email})");
                    
                    $opciones = ['Saltar'];
                    foreach ($lideresSinUsuario->take(10) as $index => $lider) {
                        $opciones[] = "{$lider->nombres} {$lider->apellidos}";
                    }
                    
                    $seleccion = $this->choice('Seleccionar líder para vincular:', $opciones, 0);
                    
                    if ($seleccion !== 'Saltar') {
                        $liderSeleccionado = $lideresSinUsuario->where('nombres', explode(' ', $seleccion)[0])->first();
                        if ($liderSeleccionado) {
                            $liderSeleccionado->usuario_id = $usuario->id;
                            $liderSeleccionado->save();
                            $this->info("✓ Usuario '{$usuario->name}' vinculado manualmente con líder '{$seleccion}'");
                            
                            $lideresSinUsuario = $lideresSinUsuario->reject(function ($lider) use ($liderSeleccionado) {
                                return $lider->id === $liderSeleccionado->id;
                            });
                        }
                    }
                }
            }
        }

        // Verificar estado final
        $usuariosLiderSinLider = User::where('role_id', $liderRole->id)->whereDoesntHave('lider')->count();
        $this->info("\n📊 Estado final:");
        $this->info("- Usuarios líder sin líder asociado: {$usuariosLiderSinLider}");
        $this->info("- Líderes sin usuario asociado: " . Lider::whereDoesntHave('usuario')->count());

        $this->info("\n¡Proceso completado!");
        return 0;
    }
}
