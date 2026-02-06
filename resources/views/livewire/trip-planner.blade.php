<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Planificador de Viajes</h1>
        <p class="mt-1 text-sm text-gray-600">Organice el transporte de votantes de manera eficiente</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div role="alert" class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded relative">
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div role="alert" class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    <!-- Progress Steps -->
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $paso >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                        1
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium {{ $paso >= 1 ? 'text-blue-600' : 'text-gray-600' }}">
                            Seleccionar Votantes
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex-1 h-1 mx-4 {{ $paso >= 2 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>

            <div class="flex-1">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $paso >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                        2
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium {{ $paso >= 2 ? 'text-blue-600' : 'text-gray-600' }}">
                            Configurar Viaje
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex-1 h-1 mx-4 {{ $paso >= 3 ? 'bg-blue-600' : 'bg-gray-200' }}"></div>

            <div class="flex-1">
                <div class="flex items-center">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $paso >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                        3
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium {{ $paso >= 3 ? 'text-blue-600' : 'text-gray-600' }}">
                            Resultado
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 1: Select Voters -->
    @if($paso === 1)
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold text-gray-900">Votantes Disponibles</h3>
                <div class="flex gap-2">
                    <button wire:click="seleccionarTodos" type="button" 
                            class="px-3 py-1 text-sm bg-blue-100 hover:bg-blue-200 text-blue-700 rounded">
                        Seleccionar Todos
                    </button>
                    <button wire:click="limpiarSeleccion" type="button" 
                            class="px-3 py-1 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded">
                        Limpiar
                    </button>
                </div>
            </div>

            <!-- Selection Counter -->
            <div class="mb-4 flex justify-between items-center">
                <div class="bg-blue-50 px-4 py-2 rounded-lg">
                    <span class="text-sm font-medium text-blue-900">
                        {{ count($votantesSeleccionados) }} votante(s) seleccionado(s)
                    </span>
                </div>
                <div class="text-sm text-gray-500">
                    Votantes que necesitan transporte y aún no votaron
                </div>
            </div>

            <!-- Filtros Aplicados Info -->
            <div class="mb-4 bg-gray-50 border border-gray-200 rounded-lg p-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="text-sm font-medium text-gray-700">
                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Filtros aplicados automáticamente
                    </h4>
                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                        {{ count($votantesDisponibles) }} de {{ $estadisticasFiltros['total'] }} votantes
                    </span>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center">
                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span>
                            Necesitan transporte
                        </span>
                        <span class="font-medium">{{ $estadisticasFiltros['necesitan_transporte'] }}</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="flex items-center">
                            <span class="w-2 h-2 bg-blue-500 rounded-full mr-2"></span>
                            Aún no han votado
                        </span>
                        <span class="font-medium">{{ $estadisticasFiltros['total'] - $estadisticasFiltros['ya_votaron'] }}</span>
                    </div>
                    
                    @if($lider_id)
                    <div class="flex items-center justify-between">
                        <span class="flex items-center">
                            <span class="w-2 h-2 bg-purple-500 rounded-full mr-2"></span>
                            Asignados a tu liderazgo
                        </span>
                        <span class="font-medium">{{ count($votantesDisponibles) }}</span>
                    </div>
                    @endif
                    
                    <div class="flex items-center justify-between">
                        <span class="flex items-center">
                            <span class="w-2 h-2 bg-red-400 rounded-full mr-2"></span>
                            Ya votaron
                        </span>
                        <span class="font-medium">{{ $estadisticasFiltros['ya_votaron'] }}</span>
                    </div>
                </div>
                
                <div class="mt-3 pt-3 border-t border-gray-200">
                    <p class="text-xs text-gray-500">
                        💡 Solo se muestran votantes que necesitan transporte y aún no han emitido su voto
                    </p>
                </div>
            </div>

            <!-- Voters List -->
            <div class="max-h-96 overflow-y-auto border border-gray-200 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 sticky top-0">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                <input type="checkbox" 
                                       @click="$wire.votantesSeleccionados.length === $wire.votantesDisponibles.length ? $wire.limpiarSeleccion() : $wire.seleccionarTodos()"
                                       class="rounded border-gray-300">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Teléfono</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dirección</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ubicación</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($votantesDisponibles as $votante)
                            <tr class="hover:bg-gray-50">
                                <td class="px-4 py-3">
                                    <input type="checkbox" 
                                           wire:click="toggleVotante({{ $votante->id }})"
                                           @checked(in_array($votante->id, $votantesSeleccionados))
                                           class="rounded border-gray-300 text-blue-600">
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">
                                    {{ $votante->nombres }} {{ $votante->apellidos }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $votante->telefono }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    {{ $votante->direccion }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-600">
                                    <div class="flex flex-col">
                                        @if($votante->barrio)
                                            <span class="font-medium">{{ $votante->barrio }}</span>
                                        @endif
                                        @if($votante->zona)
                                            <span class="text-xs text-gray-500">{{ $votante->zona }}</span>
                                        @endif
                                        @if($votante->distrito)
                                            <span class="text-xs text-gray-500">{{ $votante->distrito }}</span>
                                        @endif
                                    </div>
                                </td>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-500">
                                    No hay votantes disponibles que necesiten transporte
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @error('votantesSeleccionados')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Navigation -->
        <div class="flex justify-end">
            <button wire:click="siguientePaso" 
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">
                Continuar
            </button>
        </div>
    </div>
    @endif

    <!-- Step 2: Configure Trip -->
    @if($paso === 2)
    <div class="space-y-6">
        <!-- Destino del Viaje -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Destino del Viaje</h3>
            <p class="text-sm text-gray-600 mb-4">Especifique el destino donde se dirigirán los votantes</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Distrito -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Distrito de Destino</label>
                    <select wire:model.live="filtroDistrito" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccione distrito</option>
                        @foreach($distritosDisponibles as $distrito)
                            <option value="{{ $distrito->id }}">{{ $distrito->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Zona -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Zona de Destino</label>
                    <select wire:model.live="filtroZona" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" {{ empty($zonasDisponibles) ? 'disabled' : '' }}>
                        <option value="">{{ $filtroDistrito ? 'Seleccione zona' : 'Seleccione distrito primero' }}</option>
                        @foreach($zonasDisponibles as $zona)
                            <option value="{{ $zona->id }}">{{ $zona->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Barrio -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Barrio de Destino</label>
                    <select wire:model.live="filtroBarrio" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500" {{ empty($barriosDisponibles) ? 'disabled' : '' }}>
                        <option value="">{{ $filtroZona ? 'Seleccione barrio' : ($filtroDistrito ? 'Seleccione zona primero' : 'Seleccione distrito primero') }}</option>
                        @foreach($barriosDisponibles as $barrio)
                            <option value="{{ $barrio->id }}">{{ $barrio->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Configuración del Viaje</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vehículo *</label>
                    <select wire:model="vehiculo_id" required 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccione un vehículo</option>
                        @foreach($vehiculos as $vehiculo)
                            <option value="{{ $vehiculo->id }}">
                                {{ $vehiculo->placa }} - {{ $vehiculo->marca }} {{ $vehiculo->modelo }} 
                                (Capacidad: {{ $vehiculo->capacidad_pasajeros }})
                            </option>
                        @endforeach
                    </select>
                    @error('vehiculo_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chofer *</label>
                    <select wire:model="chofer_id" required 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Seleccione un chofer</option>
                        @foreach($choferes as $chofer)
                            <option value="{{ $chofer->id }}">
                                {{ $chofer->nombre_completo }} - {{ $chofer->telefono }}
                            </option>
                        @endforeach
                    </select>
                    @error('chofer_id') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha del Viaje *</label>
                    <input wire:model="fecha_viaje" type="date" required 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('fecha_viaje') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Hora de Salida *</label>
                    <input wire:model="hora_salida" type="time" required 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('hora_salida') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Punto de Partida *</label>
                    <input wire:model="punto_partida" type="text" required 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Ej: Local de campaña">
                    @error('punto_partida') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Costo Total del Viaje (₲) *</label>
                    <input wire:model="viaticos" type="number" min="0" step="1000" required
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                           placeholder="Ej: 150000">
                    <p class="text-xs text-gray-500 mt-1">Monto fijo total del viaje en guaraníes (incluye combustible, chofer, viáticos, etc.)</p>
                    @error('viaticos') <span class="text-red-600 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <div class="flex justify-between">
            <button wire:click="pasoAnterior" 
                    class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                Anterior
            </button>
            <button wire:click="siguientePaso" 
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg">
                Generar Plan
            </button>
        </div>
    </div>
    @endif

    <!-- Step 3: Results -->>
    @if($paso === 3)
    <div class="space-y-6">
        @if($planGenerado)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Plan de Viajes Generado</h3>

                <!-- Summary -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <div class="text-sm text-gray-600 mb-1">Total Votantes</div>
                        <div class="text-2xl font-bold text-blue-600">{{ $planGenerado['total_votantes'] }}</div>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <div class="text-sm text-gray-600 mb-1">Viajes Necesarios</div>
                        <div class="text-2xl font-bold text-green-600">{{ $planGenerado['total_viajes'] }}</div>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4">
                        <div class="text-sm text-gray-600 mb-1">Costo Total</div>
                        <div class="text-2xl font-bold text-purple-600">
                            ₲ {{ number_format($planGenerado['costo_total'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <!-- Destino Info -->
                @if(isset($planGenerado['destino_completo']))
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <h4 class="text-sm font-medium text-yellow-800 mb-1">Destino del Viaje</h4>
                    <p class="text-lg text-yellow-900">{{ $planGenerado['destino_completo']['descripcion'] }}</p>
                </div>
                @endif

                <!-- Trip Details -->
                <div class="space-y-4">
                    @foreach($planGenerado['grupos'] as $index => $grupo)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-center mb-3">
                                <h4 class="font-semibold text-gray-900">Viaje #{{ $index + 1 }}</h4>
                                <span class="text-sm bg-green-100 text-green-800 px-3 py-1 rounded-full">
                                    {{ count($grupo['votantes']) }} pasajeros • 
                                    ₲ {{ number_format($grupo['costo_fijo'], 0, ',', '.') }}
                                </span>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                                @foreach($grupo['votantes'] as $votante)
                                    <div class="text-sm text-gray-700 bg-gray-50 px-3 py-2 rounded">
                                        {{ $votante['nombres'] }} {{ $votante['apellidos'] }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between">
                <button wire:click="reiniciar" 
                        class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                    Planificar Nuevo
                </button>
                <button wire:click="confirmarYGuardar" 
                        class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg">
                    Confirmar y Guardar Viajes
                </button>
            </div>
        @endif
    </div>
    @endif
</div>
