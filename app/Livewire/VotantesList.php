<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Votante;
use App\Models\Lider;
use Illuminate\Support\Facades\Auth;

class VotantesList extends Component
{
    use WithPagination;

    public $search = '';
    public $filtroIntencion = '';
    public $filtroEstado = '';
    public $filtroEstadoVoto = '';
    public $filtroTransporte = '';
    public $filtroLider = '';
    public $filtroDistrito = '';
    public $sortBy = 'created_at';
    public $sortDir = 'desc';
    public $perPage = 50;

    public $showModal = false;
    public $editingVotante = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'filtroIntencion' => ['except' => ''],
        'filtroEstado' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDir = 'asc';
        }
    }

    public function limpiarFiltros()
    {
        $this->reset(['search', 'filtroIntencion', 'filtroEstado', 'filtroEstadoVoto', 'filtroTransporte', 'filtroLider', 'filtroDistrito']);
        $this->resetPage();
    }

    public function editarVotante($id)
    {
        $this->editingVotante = $id;
        $this->showModal = true;
    }

    public function marcarVoto($id)
    {
        $user = Auth::user();
        
        // Verificar si el usuario tiene permisos para marcar votos
        if (!$user->puedeMarcarVotos()) {
            session()->flash('error', 'No tienes permisos para marcar votos.');
            return;
        }
        
        $votante = Votante::findOrFail($id);
        $votante->ya_voto = true;
        $votante->voto_registrado_en = now();
        $votante->save();

        $this->dispatch('votante-actualizado');
        session()->flash('message', 'Voto registrado exitosamente.');
    }

    public function eliminarVotante($id)
    {
        Votante::findOrFail($id)->delete();
        $this->dispatch('votante-eliminado');
        session()->flash('message', 'Votante eliminado exitosamente.');
    }

    public function render()
    {
        $user = Auth::user();
        $query = Votante::query()->with('lider.usuario');

        // Filtrar según el rol del usuario
        if ($user->esAdmin()) {
            // Los admins ven todos los votantes sin restricciones
        } elseif ($user->esLider() && $user->lider) {
            // Los líderes solo ven sus propios votantes
            $query->where('lider_asignado_id', $user->lider->id);
        } elseif ($user->esVeedor()) {
            // Los veedores pueden ver todos los votantes pero no modificarlos
            // No aplicamos filtro adicional
        } else {
            // Si no tiene ningún rol válido, no puede ver votantes
            $query->whereRaw('1 = 0'); // Consulta que no devuelve resultados
        }

        // Búsqueda
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('nombres', 'like', '%' . $this->search . '%')
                    ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                    ->orWhere('ci', 'like', '%' . $this->search . '%')
                    ->orWhere('telefono', 'like', '%' . $this->search . '%');
            });
        }

        // Filtros
        if ($this->filtroIntencion) {
            $query->where('codigo_intencion', $this->filtroIntencion);
        }

        if ($this->filtroEstado) {
            $query->where('estado_contacto', $this->filtroEstado);
        }

        if ($this->filtroEstadoVoto !== '') {
            if ($this->filtroEstadoVoto === 'votado') {
                $query->where('ya_voto', true);
            } elseif ($this->filtroEstadoVoto === 'pendiente') {
                $query->where('ya_voto', false);
            }
        }

        if ($this->filtroTransporte !== '') {
            $query->where('necesita_transporte', $this->filtroTransporte);
        }

        if ($this->filtroLider) {
            $query->where('lider_asignado_id', $this->filtroLider);
        }

        if ($this->filtroDistrito) {
            $query->where('distrito', 'like', '%' . $this->filtroDistrito . '%');
        }

        // Ordenamiento
        $query->orderBy($this->sortBy, $this->sortDir);

        // Paginación
        if ($this->perPage === 'all') {
            $votantes = $query->get();
            // Convertir a formato compatible con paginación para la vista
            $votantes = new \Illuminate\Pagination\LengthAwarePaginator(
                $votantes,
                $votantes->count(),
                $votantes->count(),
                1,
                ['path' => request()->url()]
            );
        } else {
            $votantes = $query->paginate($this->perPage);
        }

        // Obtener líderes para filtro
        $lideres = Lider::with('usuario')->get();

        // Obtener distritos únicos para filtro
        $distritos = Votante::whereNotNull('distrito')
                            ->distinct()
                            ->pluck('distrito')
                            ->sort()
                            ->values();

        return view('livewire.votantes-list', [
            'votantes' => $votantes,
            'lideres' => $lideres,
            'distritos' => $distritos,
        ])->layout('layouts.app');
    }
}
