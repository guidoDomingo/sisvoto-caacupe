<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Distrito;
use App\Models\Zona;
use App\Models\Barrio;

class DatosMaestros extends Component
{
    public $tipoActivo = 'distritos'; // distritos, zonas, barrios
    public $mostrarModal = false;
    public $modo = 'crear'; // crear, editar
    public $itemEditando = null;

    // Propiedades del formulario
    public $nombre = '';
    public $codigo = '';
    public $descripcion = '';
    public $activo = true;
    
    // Específicas por tipo
    public $departamento = ''; // Para distritos
    public $poblacion_estimada; // Para distritos
    public $distrito_id; // Para zonas
    public $color = '#3B82F6'; // Para zonas
    public $zona_id; // Para barrios
    public $latitud; // Para barrios
    public $longitud; // Para barrios

    public function mount()
    {
        $this->reset();
    }

    public function cambiarTipo($tipo)
    {
        $this->tipoActivo = $tipo;
        $this->cerrarModal();
    }

    public function abrirModal($modo = 'crear', $id = null)
    {
        $this->modo = $modo;
        $this->mostrarModal = true;
        
        if ($modo === 'editar' && $id) {
            $this->cargarItem($id);
        } else {
            $this->limpiarFormulario();
        }
    }

    public function cerrarModal()
    {
        $this->mostrarModal = false;
        $this->limpiarFormulario();
    }

    public function guardar()
    {
        $this->validate($this->obtenerReglasValidacion());

        if ($this->modo === 'crear') {
            $this->crear();
        } else {
            $this->actualizar();
        }

        $this->cerrarModal();
        session()->flash('message', ucfirst($this->tipoActivo) . ' guardado exitosamente.');
    }

    private function crear()
    {
        $data = $this->obtenerDatosFormulario();
        
        switch ($this->tipoActivo) {
            case 'distritos':
                Distrito::create($data);
                break;
            case 'zonas':
                Zona::create($data);
                break;
            case 'barrios':
                Barrio::create($data);
                break;
        }
    }

    private function actualizar()
    {
        $data = $this->obtenerDatosFormulario();
        
        switch ($this->tipoActivo) {
            case 'distritos':
                Distrito::findOrFail($this->itemEditando)->update($data);
                break;
            case 'zonas':
                Zona::findOrFail($this->itemEditando)->update($data);
                break;
            case 'barrios':
                Barrio::findOrFail($this->itemEditando)->update($data);
                break;
        }
    }

    public function eliminar($id)
    {
        switch ($this->tipoActivo) {
            case 'distritos':
                Distrito::findOrFail($id)->delete();
                break;
            case 'zonas':
                Zona::findOrFail($id)->delete();
                break;
            case 'barrios':
                Barrio::findOrFail($id)->delete();
                break;
        }
        
        session()->flash('message', ucfirst($this->tipoActivo) . ' eliminado exitosamente.');
    }

    private function cargarItem($id)
    {
        switch ($this->tipoActivo) {
            case 'distritos':
                $item = Distrito::findOrFail($id);
                $this->nombre = $item->nombre;
                $this->codigo = $item->codigo;
                $this->descripcion = $item->descripcion;
                $this->departamento = $item->departamento;
                $this->poblacion_estimada = $item->poblacion_estimada;
                $this->activo = $item->activo;
                break;
                
            case 'zonas':
                $item = Zona::findOrFail($id);
                $this->nombre = $item->nombre;
                $this->codigo = $item->codigo;
                $this->descripcion = $item->descripcion;
                $this->distrito_id = $item->distrito_id;
                $this->color = $item->color;
                $this->activo = $item->activo;
                break;
                
            case 'barrios':
                $item = Barrio::findOrFail($id);
                $this->nombre = $item->nombre;
                $this->codigo = $item->codigo;
                $this->descripcion = $item->descripcion;
                $this->zona_id = $item->zona_id;
                $this->latitud = $item->latitud;
                $this->longitud = $item->longitud;
                $this->activo = $item->activo;
                break;
        }
        
        $this->itemEditando = $id;
    }

    private function limpiarFormulario()
    {
        $this->reset(['nombre', 'codigo', 'descripcion', 'departamento', 'poblacion_estimada', 
                     'distrito_id', 'color', 'zona_id', 'latitud', 'longitud', 'itemEditando']);
        $this->activo = true;
        $this->color = '#3B82F6';
    }

    private function obtenerReglasValidacion()
    {
        $reglas = [
            'nombre' => 'required|string|max:100',
            'codigo' => 'nullable|string|max:10',
            'descripcion' => 'nullable|string|max:1000',
            'activo' => 'boolean'
        ];

        switch ($this->tipoActivo) {
            case 'distritos':
                $reglas['departamento'] = 'nullable|string|max:100';
                $reglas['poblacion_estimada'] = 'nullable|integer|min:0';
                break;
                
            case 'zonas':
                $reglas['distrito_id'] = 'nullable|exists:distritos,id';
                $reglas['color'] = 'required|string|size:7';
                break;
                
            case 'barrios':
                $reglas['zona_id'] = 'nullable|exists:zonas,id';
                $reglas['latitud'] = 'nullable|numeric';
                $reglas['longitud'] = 'nullable|numeric';
                break;
        }

        return $reglas;
    }

    private function obtenerDatosFormulario()
    {
        $data = [
            'nombre' => $this->nombre,
            'codigo' => $this->codigo,
            'descripcion' => $this->descripcion,
            'activo' => $this->activo
        ];

        switch ($this->tipoActivo) {
            case 'distritos':
                $data['departamento'] = $this->departamento;
                $data['poblacion_estimada'] = $this->poblacion_estimada;
                break;
                
            case 'zonas':
                $data['distrito_id'] = $this->distrito_id;
                $data['color'] = $this->color;
                break;
                
            case 'barrios':
                $data['zona_id'] = $this->zona_id;
                $data['latitud'] = $this->latitud;
                $data['longitud'] = $this->longitud;
                break;
        }

        return $data;
    }

    public function render()
    {
        $distritos = Distrito::orderBy('nombre')->get();
        $zonas = Zona::with('distrito')->orderBy('nombre')->get();
        $barrios = Barrio::with('zona.distrito')->orderBy('nombre')->get();
        
        $distritos_para_select = Distrito::activo()->orderBy('nombre')->get();
        $zonas_para_select = Zona::activo()->orderBy('nombre')->get();

        return view('livewire.datos-maestros', [
            'distritos' => $distritos,
            'zonas' => $zonas,
            'barrios' => $barrios,
            'distritos_para_select' => $distritos_para_select,
            'zonas_para_select' => $zonas_para_select,
        ])->layout('layouts.app');
    }
}
