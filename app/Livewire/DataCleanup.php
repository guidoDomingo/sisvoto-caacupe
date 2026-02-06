<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Models\Votante;
use App\Models\Viaje;
use App\Models\Visita;
use App\Models\Gasto;
use App\Models\ContactoVotante;
use App\Models\Auditoria;
use App\Models\Lider;

class DataCleanup extends Component
{
    public $confirmacion = '';
    public $showModal = false;
    public $operacionSeleccionada = '';
    
    protected $listeners = ['confirmarLimpieza'];

    public function mount()
    {
        // Solo permitir acceso a super admins
        if (!Auth::user()->esAdmin()) {
            abort(403, 'No tienes permisos para acceder a esta sección.');
        }
    }

    public function abrirModal($operacion)
    {
        $this->operacionSeleccionada = $operacion;
        $this->confirmacion = '';
        $this->showModal = true;
    }

    public function cerrarModal()
    {
        $this->showModal = false;
        $this->confirmacion = '';
        $this->operacionSeleccionada = '';
    }

    public function limpiarVotantes()
    {
        if ($this->confirmacion !== 'ELIMINAR VOTANTES') {
            session()->flash('error', 'Debes escribir exactamente "ELIMINAR VOTANTES" para confirmar.');
            return;
        }

        try {
            DB::beginTransaction();
            
            // Deshabilitar foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            // Usar DELETE en lugar de TRUNCATE para mantener transacciones
            DB::table('contactos_votantes')->delete();
            DB::table('votantes')->delete();
            
            DB::commit();
            
            // Reset auto increment DESPUÉS del commit
            try {
                DB::statement('ALTER TABLE votantes AUTO_INCREMENT = 1');
                DB::statement('ALTER TABLE contactos_votantes AUTO_INCREMENT = 1');
            } catch (\Exception $autoIncrementError) {
                \Log::warning('No se pudo resetear AUTO_INCREMENT: ' . $autoIncrementError->getMessage());
            }
            
            // Rehabilitar foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            session()->flash('message', 'Todos los votantes y sus contactos han sido eliminados.');
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollback();
            }
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Exception $fkError) {
                // Ignorar errores de foreign key si la conexión está rota
            }
            session()->flash('error', 'Error al eliminar votantes: ' . $e->getMessage());
        }
        
        $this->cerrarModal();
    }

    public function limpiarViajes()
    {
        if ($this->confirmacion !== 'ELIMINAR VIAJES') {
            session()->flash('error', 'Debes escribir exactamente "ELIMINAR VIAJES" para confirmar.');
            return;
        }

        try {
            DB::beginTransaction();
            
            // Deshabilitar foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            // Usar DELETE en lugar de TRUNCATE
            DB::table('pasajeros_viaje')->delete();
            DB::table('gastos')->delete();
            DB::table('viajes')->delete();
            
            DB::commit();
            
            // Reset auto increment DESPUÉS del commit
            try {
                DB::statement('ALTER TABLE gastos AUTO_INCREMENT = 1');
                DB::statement('ALTER TABLE viajes AUTO_INCREMENT = 1');
                DB::statement('ALTER TABLE pasajeros_viaje AUTO_INCREMENT = 1');
            } catch (\Exception $autoIncrementError) {
                \Log::warning('No se pudo resetear AUTO_INCREMENT: ' . $autoIncrementError->getMessage());
            }
            
            // Rehabilitar foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            session()->flash('message', 'Todos los viajes, gastos y pasajeros han sido eliminados.');
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollback();
            }
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Exception $fkError) {
                // Ignorar errores de foreign key
            }
            session()->flash('error', 'Error al eliminar viajes: ' . $e->getMessage());
        }
        
        $this->cerrarModal();
    }

    public function limpiarVisitas()
    {
        if ($this->confirmacion !== 'ELIMINAR VISITAS') {
            session()->flash('error', 'Debes escribir exactamente "ELIMINAR VISITAS" para confirmar.');
            return;
        }

        try {
            DB::beginTransaction();
            
            // Deshabilitar foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            DB::table('visitas')->delete();
            
            DB::commit();
            
            // Reset auto increment DESPUÉS del commit
            try {
                DB::statement('ALTER TABLE visitas AUTO_INCREMENT = 1');
            } catch (\Exception $autoIncrementError) {
                \Log::warning('No se pudo resetear AUTO_INCREMENT: ' . $autoIncrementError->getMessage());
            }
            
            // Rehabilitar foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            session()->flash('message', 'Todas las visitas han sido eliminadas.');
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollback();
            }
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Exception $fkError) {
                // Ignorar errores de foreign key
            }
            session()->flash('error', 'Error al eliminar visitas: ' . $e->getMessage());
        }
        
        $this->cerrarModal();
    }

    public function limpiarAuditorias()
    {
        if ($this->confirmacion !== 'ELIMINAR AUDITORIAS') {
            session()->flash('error', 'Debes escribir exactamente "ELIMINAR AUDITORIAS" para confirmar.');
            return;
        }

        try {
            DB::table('auditorias')->delete();
            
            // Reset auto increment
            try {
                DB::statement('ALTER TABLE auditorias AUTO_INCREMENT = 1');
            } catch (\Exception $autoIncrementError) {
                \Log::warning('No se pudo resetear AUTO_INCREMENT: ' . $autoIncrementError->getMessage());
            }
            
            session()->flash('message', 'Todas las auditorías han sido eliminadas.');
        } catch (\Exception $e) {
            session()->flash('error', 'Error al eliminar auditorías: ' . $e->getMessage());
        }
        
        $this->cerrarModal();
    }

    public function resetearCompleto()
    {
        if ($this->confirmacion !== 'RESETEAR SISTEMA COMPLETO') {
            session()->flash('error', 'Debes escribir exactamente "RESETEAR SISTEMA COMPLETO" para confirmar.');
            return;
        }

        try {
            // Usar transacción solo para operaciones que la soportan
            DB::beginTransaction();
            
            // Deshabilitar foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            // Usar DELETE en lugar de TRUNCATE para mantener transacciones
            DB::table('pasajeros_viaje')->delete();
            DB::table('contactos_votantes')->delete();
            DB::table('gastos')->delete();
            DB::table('visitas')->delete();
            DB::table('viajes')->delete();
            DB::table('votantes')->delete();
            DB::table('auditorias')->delete();
            
            // Eliminar usuarios que no sean el admin actual
            User::where('id', '!=', Auth::id())->delete();
            
            // Eliminar líderes excepto el asociado al admin actual si existe
            if (Auth::user()->lider) {
                Lider::where('id', '!=', Auth::user()->lider->id)->delete();
            } else {
                DB::table('lideres')->delete();
            }
            
            DB::commit();
            
            // Reset auto increments DESPUÉS del commit (fuera de la transacción)
            try {
                $tables = ['votantes', 'contactos_votantes', 'gastos', 'visitas', 'viajes', 'auditorias', 'lideres', 'pasajeros_viaje'];
                foreach ($tables as $table) {
                    DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1");
                }
            } catch (\Exception $autoIncrementError) {
                // Si falla el reset de auto increment, no es crítico, solo guardar en log
                \Log::warning('No se pudo resetear AUTO_INCREMENT: ' . $autoIncrementError->getMessage());
            }
            
            // Rehabilitar foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            session()->flash('message', 'Sistema completamente reseteado. Solo tu usuario administrador se mantuvo.');
            
        } catch (\Exception $e) {
            // Solo hacer rollback si hay una transacción activa
            if (DB::transactionLevel() > 0) {
                DB::rollback();
            }
            
            // Asegurar que foreign key checks esté habilitado en caso de error
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Exception $fkError) {
                // Ignorar errores de foreign key si la conexión está rota
            }
            
            session()->flash('error', 'Error al resetear el sistema: ' . $e->getMessage());
        }
        
        $this->cerrarModal();
    }

    public function getEstadisticas()
    {
        return [
            'votantes' => Votante::count(),
            'contactos' => ContactoVotante::count(),
            'viajes' => Viaje::count(),
            'gastos' => Gasto::count(),
            'visitas' => Visita::count(),
            'auditorias' => Auditoria::count(),
            'usuarios' => User::count(),
            'lideres' => Lider::count()
        ];
    }

    public function render()
    {
        return view('livewire.data-cleanup', [
            'estadisticas' => $this->getEstadisticas()
        ])->layout('layouts.app');
    }
}
