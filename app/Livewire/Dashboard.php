<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Votante;
use App\Models\Viaje;
use App\Models\Gasto;
use App\Models\Lider;
use App\Services\PredictionService;
use App\Services\MetricsService;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $metricas = [];
    public $prediccion = [];
    public $gastosRecientes = [];
    public $viajesProximos = [];
    public $lideresTop = [];

    public function mount()
    {
        $this->cargarDatos();
    }

    public function cargarDatos()
    {
        $user = Auth::user();
        $predictionService = new PredictionService();
        $metricsService = new MetricsService();

        if ($user->esAdmin()) {
            // Métricas generales para admins
            $this->metricas = $metricsService->getGeneralMetrics();
            $this->prediccion = $predictionService->heuristicPrediction();
            
            // Líderes top por rendimiento
            $this->lideresTop = Lider::withCount('votantes')
                ->with('usuario')
                ->orderBy('votantes_count', 'desc')
                ->take(5)
                ->get();

            // Gastos recientes
            $this->gastosRecientes = Gasto::with('usuarioRegistro', 'viaje')
                ->orderBy('fecha_gasto', 'desc')
                ->take(5)
                ->get();

            // Viajes próximos
            $this->viajesProximos = Viaje::with('vehiculo', 'chofer', 'liderResponsable')
                ->where('fecha_viaje', '>=', now())
                ->where('estado', '!=', 'Completado')
                ->orderBy('fecha_viaje')
                ->take(5)
                ->get();
        } 
        elseif ($user->esLider() && $user->lider) {
            // Métricas específicas para líderes
            $lider = $user->lider;
            $votantesAsignados = $lider->votantes();
            
            $this->metricas = [
                'total_votantes' => $votantesAsignados->count(),
                'ya_votaron' => $votantesAsignados->where('ya_voto', true)->count(),
                'porcentaje_votacion' => $votantesAsignados->count() > 0 ? 
                    round(($votantesAsignados->where('ya_voto', true)->count() / $votantesAsignados->count()) * 100, 1) : 0,
                'necesitan_transporte' => $votantesAsignados->where('necesita_transporte', true)->count(),
                'contactados' => $votantesAsignados->where('estado_contacto', '!=', 'Nuevo')->count(),
                'intencion_a_b' => $votantesAsignados->whereIn('codigo_intencion', ['A', 'B'])->count(),
                'por_intencion' => [
                    'A' => $votantesAsignados->where('codigo_intencion', 'A')->count(),
                    'B' => $votantesAsignados->where('codigo_intencion', 'B')->count(),
                    'C' => $votantesAsignados->where('codigo_intencion', 'C')->count(),
                    'D' => $votantesAsignados->where('codigo_intencion', 'D')->count(),
                    'E' => $votantesAsignados->where('codigo_intencion', 'E')->count(),
                ]
            ];

            // Predicción para el líder específico
            $this->prediccion = $predictionService->heuristicPredictionForLeader($lider);
            
            // Solo mostrar gastos relacionados con este líder
            $this->gastosRecientes = Gasto::with('usuarioRegistro', 'viaje')
                ->whereHas('viaje', function($query) use ($lider) {
                    $query->where('lider_responsable_id', $lider->id);
                })
                ->orWhere('usuario_registro_id', $user->id)
                ->orderBy('fecha_gasto', 'desc')
                ->take(5)
                ->get();

            // Viajes donde este líder es responsable
            $this->viajesProximos = Viaje::with('vehiculo', 'chofer', 'liderResponsable')
                ->where('lider_responsable_id', $lider->id)
                ->where('fecha_viaje', '>=', now())
                ->where('estado', '!=', 'Completado')
                ->orderBy('fecha_viaje')
                ->take(5)
                ->get();

            // No mostrar líderes top para líderes (solo para admins)
            $this->lideresTop = collect();
        }
        else {
            // Para veedores u otros roles, mostrar datos básicos
            $this->metricas = [
                'total_votantes' => 0,
                'ya_votaron' => 0,
                'porcentaje_votacion' => 0,
                'necesitan_transporte' => 0,
            ];
            $this->prediccion = [];
            $this->gastosRecientes = collect();
            $this->viajesProximos = collect();
            $this->lideresTop = collect();
        }
    }

    public function render()
    {
        return view('livewire.dashboard')
            ->layout('layouts.app');
    }
}
