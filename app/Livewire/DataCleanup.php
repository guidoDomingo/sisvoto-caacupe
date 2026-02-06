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
            
            // Eliminar registros relacionados primero
            ContactoVotante::truncate();
            Votante::truncate();
            
            // Rehabilitar foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            // Reset auto increment
            DB::statement('ALTER TABLE votantes AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE contactos_votantes AUTO_INCREMENT = 1');
            
            DB::commit();
            session()->flash('message', 'Todos los votantes y sus contactos han sido eliminados.');
        } catch (\Exception $e) {
            DB::rollback();
            // Asegurar que foreign key checks esté habilitado en caso de error
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
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
            
            // Eliminar en orden correcto
            DB::table('pasajeros_viaje')->delete();
            Gasto::truncate();
            Viaje::truncate();
            
            // Rehabilitar foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            // Reset auto increment
            DB::statement('ALTER TABLE gastos AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE viajes AUTO_INCREMENT = 1');
            DB::statement('ALTER TABLE pasajeros_viaje AUTO_INCREMENT = 1');
            
            DB::commit();
            session()->flash('message', 'Todos los viajes, gastos y pasajeros han sido eliminados.');
        } catch (\Exception $e) {
            DB::rollback();
            // Asegurar que foreign key checks esté habilitado en caso de error
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
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
            
            Visita::truncate();
            
            // Rehabilitar foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            DB::statement('ALTER TABLE visitas AUTO_INCREMENT = 1');
            
            DB::commit();
            session()->flash('message', 'Todas las visitas han sido eliminadas.');
        } catch (\Exception $e) {
            DB::rollback();
            // Asegurar que foreign key checks esté habilitado en caso de error
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
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
            Auditoria::truncate();
            DB::statement('ALTER TABLE auditorias AUTO_INCREMENT = 1');
            
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
            DB::beginTransaction();
            
            // Deshabilitar foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            
            // Eliminar en orden para respetar foreign keys
            DB::table('pasajeros_viaje')->delete();
            ContactoVotante::truncate();
            Gasto::truncate();
            Visita::truncate();
            Viaje::truncate();
            Votante::truncate();
            Auditoria::truncate();
            
            // Eliminar usuarios que no sean el admin actual
            User::where('id', '!=', Auth::id())->delete();
            
            // Eliminar líderes excepto el asociado al admin actual si existe
            if (Auth::user()->lider) {
                Lider::where('id', '!=', Auth::user()->lider->id)->delete();
            } else {
                Lider::truncate();
            }
            
            // Rehabilitar foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
            
            // Reset auto increments
            $tables = ['votantes', 'contactos_votantes', 'gastos', 'visitas', 'viajes', 'auditorias', 'lideres', 'pasajeros_viaje'];
            foreach ($tables as $table) {
                DB::statement("ALTER TABLE {$table} AUTO_INCREMENT = 1");
            }
            
            DB::commit();
            session()->flash('message', 'Sistema completamente reseteado. Solo tu usuario administrador se mantuvo.');
        } catch (\Exception $e) {
            DB::rollback();
            // Asegurar que foreign key checks esté habilitado en caso de error
            DB::statement('SET FOREIGN_KEY_CHECKS=1');
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
