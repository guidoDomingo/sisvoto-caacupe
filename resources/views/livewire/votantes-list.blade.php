<div>
    <!-- Header -->
    <div class="mb-4 lg:mb-6">
        <h1 class="text-xl lg:text-2xl font-bold text-gray-900">Gestión de Votantes</h1>
        <p class="mt-1 text-sm text-gray-600">Administra y consulta la base de datos de votantes</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div role="alert" class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif

    <!-- Search Bar (Mobile-First) -->
    <div class="bg-white rounded-lg shadow p-4 mb-4">
        <div class="relative">
            <input wire:model.live.debounce.300ms="search" type="text" 
                   placeholder="🔍 Buscar por nombre, CI, teléfono..." 
                   class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 pl-10">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                </svg>
            </div>
            @if($search)
                <button wire:click="$set('search', '')" 
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-red-600">
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            @endif
        </div>
    </div>

    <!-- Mobile Filters -->
    <div class="lg:hidden bg-white rounded-lg shadow p-4 mb-4">
        <div class="space-y-3">
            <!-- Row 1 -->
            <div class="grid grid-cols-2 gap-3">
                <select wire:model.live="filtroIntencion" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">🎯 Intención</option>
                    <option value="A">👍 A - Seguro</option>
                    <option value="B">😊 B - Probable</option>
                    <option value="C">🤔 C - Indeciso</option>
                    <option value="D">😕 D - Difícil</option>
                    <option value="E">❌ E - Contrario</option>
                </select>
                <select wire:model.live="filtroEstadoVoto" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">🗳️ Voto</option>
                    <option value="pendiente">⏳ Pendiente</option>
                    <option value="votado">✅ Ya votó</option>
                </select>
            </div>
            <!-- Row 2 -->
            <div class="grid grid-cols-2 gap-3">
                <select wire:model.live="filtroEstado" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">📞 Estado</option>
                    <option value="Nuevo">🆕 Nuevo</option>
                    <option value="Contactado">📱 Contactado</option>
                    <option value="Re-contacto">🔄 Re-contacto</option>
                    <option value="Comprometido">✅ Comprometido</option>
                    <option value="Crítico">🚨 Crítico</option>
                </select>
                <select wire:model.live="filtroLider" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="">👤 Líder</option>
                    @foreach($lideres as $lider)
                        <option value="{{ $lider->id }}">{{ $lider->usuario->name }}</option>
                    @endforeach
                </select>
            </div>
            <!-- Row 3 -->
            <select wire:model.live="filtroDistrito" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                <option value="">📍 Todos los distritos</option>
                @foreach($distritos as $distrito)
                    <option value="{{ $distrito }}">{{ $distrito }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Desktop Filters & Actions -->
    <div class="hidden lg:block bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar por nombre, CI, teléfono..." 
                    class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>

            <!-- Intención -->
            <select wire:model.live="filtroIntencion" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Todas las intenciones</option>
                <option value="A">A - Seguro</option>
                <option value="B">B - Probable</option>
                <option value="C">C - Indeciso</option>
                <option value="D">D - Difícil</option>
                <option value="E">E - Contrario</option>
            </select>

            <!-- Estado -->
            <select wire:model.live="filtroEstado" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Todos los estados</option>
                <option value="Nuevo">Nuevo</option>
                <option value="Contactado">Contactado</option>
                <option value="Re-contacto">Re-contacto</option>
                <option value="Comprometido">Comprometido</option>
                <option value="Crítico">Crítico</option>
            </select>

            <!-- Estado de Voto -->
            <select wire:model.live="filtroEstadoVoto" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Todos los votos</option>
                <option value="pendiente">Pendiente</option>
                <option value="votado">Ya votó</option>
            </select>
        </div>

        <!-- Segunda fila de filtros para datos TSJE -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <!-- Distrito -->
            <select wire:model.live="filtroDistrito" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Todos los distritos</option>
                @foreach($distritos as $distrito)
                    <option value="{{ $distrito }}">{{ $distrito }}</option>
                @endforeach
            </select>

            <!-- Líder -->
            <select wire:model.live="filtroLider" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">Todos los líderes</option>
                @foreach($lideres as $lider)
                    <option value="{{ $lider->id }}">{{ $lider->usuario->name }}</option>
                @endforeach
            </select>

            <!-- Espacio para futuro filtro -->
            <div></div>
        </div>

        <div class="flex flex-wrap gap-2 justify-between">
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('votantes.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                    </svg>
                    Nuevo Votante
                </a>
                
                <button wire:click="limpiarFiltros" class="inline-flex items-center px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                    </svg>
                    Limpiar
                </button>

                <button wire:click="exportarExcel" 
                        wire:loading.attr="disabled"
                        wire:target="exportarExcel"
                        class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 disabled:bg-gray-400 text-white font-medium rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" wire:loading.remove wire:target="exportarExcel">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                    <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" wire:loading wire:target="exportarExcel">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading.remove wire:target="exportarExcel">📊 Exportar Excel</span>
                    <span wire:loading wire:target="exportarExcel">Generando...</span>
                </button>
            </div>
            
            <div class="flex items-center gap-2">
                <label class="text-sm text-gray-600">Mostrar:</label>
                <select wire:model.live="perPage" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">Todos</option>
                </select>
                <span class="text-sm text-gray-600">resultados</span>
            </div>
        </div>
    </div>

    <!-- Mobile Actions -->
    <div class="lg:hidden bg-white rounded-lg shadow p-4 mb-4">
        <div class="flex flex-wrap gap-2 justify-between items-center">
            <div class="flex gap-2">
                <a href="{{ route('votantes.create') }}" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg text-sm">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                    </svg>
                    Nuevo
                </a>
                
                <button wire:click="limpiarFiltros" class="inline-flex items-center px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg text-sm">
                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                    </svg>
                    Limpiar
                </button>
            </div>
            
            <div class="flex items-center gap-2">
                <select wire:model.live="perPage" class="border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <span class="text-sm text-gray-600">resultados</span>
            </div>
        </div>
    </div>

    <!-- Results Counter -->
    <div class="mb-4 text-sm">
        <div class="flex flex-wrap items-center gap-4">
            <div class="text-gray-600">
                @if($search)
                    Mostrando resultados para "<strong class="text-blue-600">{{ $search }}</strong>": 
                @endif
                <strong class="text-gray-900">{{ ($votantes instanceof \Illuminate\Pagination\LengthAwarePaginator) ? $votantes->total() : $votantes->count() }}</strong> votantes
            </div>
            
            @if($search || $filtroIntencion || $filtroEstado || $filtroEstadoVoto || $filtroLider || $filtroDistrito)
                <div class="flex flex-wrap gap-1">
                    <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">
                        Filtros activos
                    </span>
                </div>
            @endif
            
            @if($search && strlen($search) < 2)
                <div class="flex items-center gap-1 text-amber-600">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-xs">Mínimo 2 caracteres para buscar</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Mobile Cards View -->
    <div class="lg:hidden space-y-4">
        @forelse($votantes as $votante)
            <div class="bg-white rounded-lg shadow border {{ $votante->ya_voto ? 'border-green-200 bg-green-50' : 'border-gray-200' }}">
                <!-- Card Header -->
                <div class="p-4 border-b border-gray-100">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <h3 class="text-base font-semibold text-gray-900">
                                {{ $votante->nombres }} {{ $votante->apellidos }}
                            </h3>
                            <p class="text-sm text-gray-600">CI: {{ $votante->ci }}</p>
                            @if($votante->telefono)
                                <p class="text-sm text-gray-600">📞 {{ $votante->telefono }}</p>
                            @endif
                        </div>
                        <div class="flex flex-col items-end gap-1">
                            <!-- Intención -->
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($votante->codigo_intencion === 'A') bg-green-100 text-green-800
                                @elseif($votante->codigo_intencion === 'B') bg-blue-100 text-blue-800
                                @elseif($votante->codigo_intencion === 'C') bg-yellow-100 text-yellow-800
                                @elseif($votante->codigo_intencion === 'D') bg-orange-100 text-orange-800
                                @else bg-red-100 text-red-800
                                @endif">
                                {{ $votante->codigo_intencion }}
                            </span>
                            <!-- Estado de Voto -->
                            @if($votante->ya_voto)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    ✅ Ya votó
                                </span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                    ⏳ Pendiente
                                </span>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Card Body -->
                <div class="p-4">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <!-- Columna Izquierda -->
                        <div class="space-y-2">
                            @if($votante->distrito)
                                <div>
                                    <span class="text-gray-500">Distrito:</span>
                                    <p class="font-medium">{{ $votante->distrito }}</p>
                                </div>
                            @endif
                            
                            @if($votante->mesa)
                                <div>
                                    <span class="text-gray-500">Mesa:</span>
                                    <p class="font-medium">
                                        {{ $votante->mesa }}
                                        @if($votante->orden) / Orden {{ $votante->orden }}@endif
                                    </p>
                                </div>
                            @endif

                            @if($votante->lider && $votante->lider->usuario)
                                <div>
                                    <span class="text-gray-500">Líder:</span>
                                    <p class="font-medium">{{ $votante->lider->usuario->name }}</p>
                                </div>
                            @endif
                        </div>

                        <!-- Columna Derecha -->
                        <div class="space-y-2">
                            <div>
                                <span class="text-gray-500">Estado:</span>
                                <p class="font-medium">{{ $votante->estado_contacto }}</p>
                            </div>

                            @if($votante->descripcion_local || $votante->local_votacion)
                                <div>
                                    <span class="text-gray-500">Local:</span>
                                    <p class="font-medium text-xs">{{ $votante->descripcion_local ?? $votante->local_votacion }}</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Badges -->
                    <div class="flex flex-wrap gap-2 mt-3">
                        @if($votante->necesita_transporte)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                🚗 Transporte
                            </span>
                        @endif
                        @if($votante->nro_registro)
                            <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                📋 TSJE
                            </span>
                        @endif
                        @if($votante->voto_registrado_en)
                            <span class="text-xs text-gray-500">
                                Votó: {{ $votante->voto_registrado_en->format('d/m H:i') }}
                            </span>
                        @endif
                    </div>

                    <!-- Actions -->
                    <div class="flex justify-end gap-2 mt-4 pt-3 border-t border-gray-100">
                        @if(!$votante->ya_voto && auth()->user()->puedeMarcarVotos())
                            <button wire:click="marcarVoto({{ $votante->id }})" 
                                    wire:confirm="¿Confirmar que este votante ya votó?"
                                    class="inline-flex items-center px-3 py-1 text-xs font-medium text-green-600 bg-green-50 rounded hover:bg-green-100">
                                ✓ Marcar Voto
                            </button>
                        @endif
                        
                        @if(auth()->user()->puedeCrearVotantes() || auth()->user()->esAdmin())
                            <a href="{{ route('votantes.edit', $votante->id) }}" 
                               class="inline-flex items-center px-3 py-1 text-xs font-medium text-blue-600 bg-blue-50 rounded hover:bg-blue-100">
                                ✏️ Editar
                            </a>
                        @endif
                        
                        @if(auth()->user()->esAdmin())
                            <button wire:click="eliminarVotante({{ $votante->id }})" 
                                    wire:confirm="¿Está seguro de eliminar este votante?"
                                    class="inline-flex items-center px-3 py-1 text-xs font-medium text-red-600 bg-red-50 rounded hover:bg-red-100">
                                🗑️ Eliminar
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-lg shadow p-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 12a1 1 0 102 0V6.414l1.293 1.293a1 1 0 101.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 101.414 1.414L9 6.414V12zM4 15a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1z"></path>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron votantes</h3>
                <p class="text-gray-600 mb-4">No hay votantes que coincidan con los filtros aplicados.</p>
                @if($search || $filtroIntencion || $filtroEstado || $filtroEstadoVoto || $filtroLider || $filtroDistrito)
                    <button wire:click="limpiarFiltros" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">
                        Limpiar filtros
                    </button>
                @else
                    <a href="{{ route('votantes.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                        </svg>
                        Agregar primer votante
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    <!-- Desktop Table View -->
    <div class="hidden lg:block bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th wire:click="sortBy('ci')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            CI
                        </th>
                        <th wire:click="sortBy('nombres')" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                            Nombre
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Teléfono
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Lugar de Votación
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Mesa/Orden
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Distrito
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Líder
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Intención
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado de Contacto
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Estado de Voto
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($votantes as $votante)
                        <tr class="hover:bg-gray-50 {{ $votante->ya_voto ? 'bg-green-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $votante->ci }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $votante->nombres }} {{ $votante->apellidos }}</div>
                                <div class="flex items-center gap-1 mt-1">
                                    @if($votante->necesita_transporte)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z"></path>
                                            </svg>
                                            Transporte
                                        </span>
                                    @endif
                                    @if($votante->nro_registro)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800" 
                                              title="Número de registro TSJE: {{ $votante->nro_registro }}">
                                            TSJE
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $votante->telefono ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <div class="max-w-xs truncate" title="{{ $votante->descripcion_local ?? $votante->local_votacion ?? $votante->direccion }}">
                                    {{ $votante->descripcion_local ?? $votante->local_votacion ?? $votante->direccion ?? '-' }}
                                </div>
                                @if($votante->local_votacion && $votante->descripcion_local)
                                    <div class="text-xs text-gray-400">Cod: {{ $votante->local_votacion }}</div>
                                @elseif($votante->seccion)
                                    <div class="text-xs text-gray-400">Secc: {{ $votante->seccion }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                @if($votante->mesa)
                                    <div class="text-sm font-medium">Mesa {{ $votante->mesa }}</div>
                                    @if($votante->orden)
                                        <div class="text-xs text-gray-400">Orden {{ $votante->orden }}</div>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                <div>{{ $votante->distrito ?? '-' }}</div>
                                @if($votante->departamento && $votante->departamento !== $votante->distrito)
                                    <div class="text-xs text-gray-400">{{ $votante->departamento }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $votante->lider->usuario->name ?? '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($votante->codigo_intencion === 'A') bg-green-100 text-green-800
                                    @elseif($votante->codigo_intencion === 'B') bg-blue-100 text-blue-800
                                    @elseif($votante->codigo_intencion === 'C') bg-yellow-100 text-yellow-800
                                    @elseif($votante->codigo_intencion === 'D') bg-orange-100 text-orange-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ $votante->codigo_intencion }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                {{ $votante->estado_contacto }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($votante->ya_voto)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Ya votó
                                    </span>
                                    @if($votante->voto_registrado_en)
                                        <div class="text-xs text-gray-400 mt-1">
                                            {{ $votante->voto_registrado_en->format('d/m H:i') }}
                                        </div>
                                    @endif
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Pendiente
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex gap-2">
                                    @if(!$votante->ya_voto && auth()->user()->puedeMarcarVotos())
                                        <button wire:click="marcarVoto({{ $votante->id }})" 
                                                wire:confirm="¿Confirmar que este votante ya votó?"
                                                class="text-green-600 hover:text-green-900" title="Marcar voto">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    @endif
                                    
                                    @if(auth()->user()->puedeCrearVotantes() || auth()->user()->esAdmin())
                                        <a href="{{ route('votantes.edit', $votante->id) }}" class="text-blue-600 hover:text-blue-900" title="Editar">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"></path>
                                            </svg>
                                        </a>
                                    @endif
                                    
                                    @if(auth()->user()->esAdmin())
                                        <button wire:click="eliminarVotante({{ $votante->id }})" 
                                                wire:confirm="¿Está seguro de eliminar este votante?"
                                                class="text-red-600 hover:text-red-900" title="Eliminar">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-6 py-4 text-center text-sm text-gray-500">
                                No se encontraron votantes
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200">
            {{ $votantes->links() }}
        </div>
    </div>

    <!-- Mobile Pagination -->
    <div class="lg:hidden mt-4">
        {{ $votantes->links() }}
    </div>
</div>