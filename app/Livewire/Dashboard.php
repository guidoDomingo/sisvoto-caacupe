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

        // Métricas generales
        // Temporalmente cargar métricas generales para todos los usuarios
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

    public function render()
    {
        return view('livewire.dashboard')
            ->layout('layouts.app');
    }
}
