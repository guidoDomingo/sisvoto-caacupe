<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Lider;
use App\Models\Votante;

class CheckVotantes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'votantes:check {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verificar votantes asignados a un líder';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'lider@test.com';
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Usuario con email {$email} no encontrado");
            return;
        }
        
        $this->info("Usuario: {$user->name}");
        $this->info("Email: {$user->email}");
        $this->info("Rol: {$user->role->nombre}");
        $this->info("Es líder: " . ($user->esLider() ? 'SÍ' : 'NO'));
        
        if ($user->lider) {
            $this->info("Líder ID: {$user->lider->id}");
            $votantesCount = $user->lider->votantes()->count();
            $this->info("Votantes asignados: {$votantesCount}");
            
            if ($votantesCount > 0) {
                $this->info("\nPrimeros 5 votantes:");
                $votantes = $user->lider->votantes()->limit(5)->get();
                foreach ($votantes as $votante) {
                    $this->line("- {$votante->nombres} {$votante->apellidos} (CI: {$votante->ci})");
                }
            }
        } else {
            $this->error("El usuario no tiene registro de líder asociado");
        }
        
        $this->info("\nEstadísticas generales:");
        $this->info("Total votantes en sistema: " . Votante::count());
        $this->info("Votantes sin líder asignado: " . Votante::whereNull('lider_asignado_id')->count());
        $this->info("Líderes con votantes: " . Lider::has('votantes')->count());
    }
}
