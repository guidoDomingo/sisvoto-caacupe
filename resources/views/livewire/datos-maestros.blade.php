<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Datos Maestros</h1>
        <p class="mt-1 text-sm text-gray-600">Gestione barrios, zonas y distritos para organizar votantes y planificar actividades</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div role="alert" class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded relative">
            <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            {{ session('message') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div role="alert" class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded relative">
            <svg class="w-5 h-5 inline mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Tabs Navigation -->
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8 px-6" aria-label="Tabs">
                <button wire:click="cambiarTipo('distritos')" 
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm 
                               {{ $tipoActivo === 'distritos' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    🏛️ Distritos
                </button>
                <button wire:click="cambiarTipo('zonas')" 
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm 
                               {{ $tipoActivo === 'zonas' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    🗺️ Zonas
                </button>
                <button wire:click="cambiarTipo('barrios')" 
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm 
                               {{ $tipoActivo === 'barrios' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    🏘️ Barrios
                </button>
            </nav>
        </div>
    </div>

    <!-- Content Area -->
    <div class="bg-white rounded-lg shadow">
        <!-- Header with Action Button -->
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-medium text-gray-900">
                    Gestión de {{ ucfirst($tipoActivo) }}
                </h3>
                <p class="text-sm text-gray-500">
                    @if($tipoActivo === 'distritos')
                        Administre los distritos administrativos
                    @elseif($tipoActivo === 'zonas')
                        Gestione las zonas dentro de cada distrito
                    @else
                        Configure los barrios dentro de las zonas
                    @endif
                </p>
            </div>
            <button wire:click="abrirModal('crear')" 
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Nuevo {{ ucfirst(substr($tipoActivo, 0, -1)) }}
            </button>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            @if($tipoActivo === 'distritos')
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Distrito</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Departamento</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Población</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($distritos as $distrito)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $distrito->nombre }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($distrito->descripcion, 50) }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-md font-mono">{{ $distrito->codigo }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $distrito->departamento ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $distrito->poblacion_estimada ? number_format($distrito->poblacion_estimada) : 'N/A' }}
                                @if($distrito->zonas_count > 0)
                                    <div class="text-xs text-blue-600 mt-1">{{ $distrito->zonas_count }} zona(s)</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full {{ $distrito->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $distrito->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button wire:click="abrirModal('editar', {{ $distrito->id }})" 
                                        class="text-blue-600 hover:text-blue-900 mr-3">Editar</button>
                                @if($distrito->zonas_count == 0)
                                    <button wire:click="eliminar({{ $distrito->id }})" 
                                            onclick="return confirm('¿Está seguro de eliminar este distrito?')"
                                            class="text-red-600 hover:text-red-900">Eliminar</button>
                                @else
                                    <span class="text-gray-400 cursor-not-allowed" title="No se puede eliminar: tiene {{ $distrito->zonas_count }} zona(s) asociada(s)">Eliminar</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No hay distritos registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            @elseif($tipoActivo === 'zonas')
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zona</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Distrito</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($zonas as $zona)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $zona->nombre }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($zona->descripcion, 50) }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-md font-mono">{{ $zona->codigo }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $zona->distrito->nombre ?? 'Sin distrito' }}
                                @if($zona->barrios_count > 0)
                                    <div class="text-xs text-blue-600 mt-1">{{ $zona->barrios_count }} barrio(s)</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($zona->color)
                                        <div class="w-6 h-6 rounded-full mr-2 border border-gray-300" 
                                             style="background-color:{{ $zona->color }}"></div>
                                    @else
                                        <div class="w-6 h-6 rounded-full mr-2 bg-gray-200"></div>
                                    @endif
                                    <span class="text-sm text-gray-900">{{ $zona->color ?? '#CCCCCC' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full {{ $zona->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $zona->activo ? 'Activa' : 'Inactiva' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button wire:click="abrirModal('editar', {{ $zona->id }})" 
                                        class="text-blue-600 hover:text-blue-900 mr-3">Editar</button>
                                @if($zona->barrios_count == 0)
                                    <button wire:click="eliminar({{ $zona->id }})" 
                                            onclick="return confirm('¿Está seguro de eliminar esta zona?')"
                                            class="text-red-600 hover:text-red-900">Eliminar</button>
                                @else
                                    <span class="text-gray-400 cursor-not-allowed" title="No se puede eliminar: tiene {{ $zona->barrios_count }} barrio(s) asociado(s)">Eliminar</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No hay zonas registradas
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            @else
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barrio</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Zona</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ubicación</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($barrios as $barrio)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <div class="text-sm font-medium text-gray-900">{{ $barrio->nombre }}</div>
                                    <div class="text-sm text-gray-500">{{ Str::limit($barrio->descripcion, 50) }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <span class="bg-gray-100 text-gray-800 px-2 py-1 rounded-md font-mono">{{ $barrio->codigo }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $barrio->zona->nombre ?? 'Sin zona' }}
                                <div class="text-xs text-gray-500">{{ $barrio->zona->distrito->nombre ?? '' }}</div>
                                @if($barrio->votantes_count > 0)
                                    <div class="text-xs text-blue-600 mt-1">{{ $barrio->votantes_count }} votante(s)</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($barrio->latitud && $barrio->longitud)
                                    <div class="text-xs font-mono">
                                        {{ number_format($barrio->latitud, 4) }}, {{ number_format($barrio->longitud, 4) }}
                                    </div>
                                @else
                                    <span class="text-gray-400">Sin coordenadas</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs rounded-full {{ $barrio->activo ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $barrio->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button wire:click="abrirModal('editar', {{ $barrio->id }})" 
                                        class="text-blue-600 hover:text-blue-900 mr-3">Editar</button>
                                @if($barrio->votantes_count == 0)
                                    <button wire:click="eliminar({{ $barrio->id }})" 
                                            onclick="return confirm('¿Está seguro de eliminar este barrio?')"
                                            class="text-red-600 hover:text-red-900">Eliminar</button>
                                @else
                                    <span class="text-gray-400 cursor-not-allowed" title="No se puede eliminar: tiene {{ $barrio->votantes_count }} votante(s) asociado(s)">Eliminar</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                No hay barrios registrados
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <!-- Modal for Create/Edit -->
    @if($mostrarModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50" 
             wire:click.self="cerrarModal">
            <div class="relative top-20 mx-auto p-5 border w-11/12 max-w-lg shadow-lg rounded-lg bg-white">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $modo === 'crear' ? 'Crear nuevo' : 'Editar' }} {{ ucfirst(substr($tipoActivo, 0, -1)) }}
                    </h3>
                </div>

                <form wire:submit.prevent="guardar">
                    <div class="space-y-4">
                        <!-- Nombre -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nombre *
                            </label>
                            <input type="text" 
                                   wire:model="nombre"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   required>
                            @error('nombre') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Código -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Código
                            </label>
                            <input type="text" 
                                   wire:model="codigo"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('codigo') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Descripción -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Descripción
                            </label>
                            <textarea wire:model="descripcion"
                                      rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                            @error('descripcion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Campos específicos por tipo -->
                        @if($tipoActivo === 'distritos')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Departamento
                                    </label>
                                    <input type="text" 
                                           wire:model="departamento"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('departamento') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Población Estimada
                                    </label>
                                    <input type="number" 
                                           wire:model="poblacion_estimada"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('poblacion_estimada') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @elseif($tipoActivo === 'zonas')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Distrito
                                    </label>
                                    <select wire:model="distrito_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Seleccione distrito</option>
                                        @foreach($distritos_para_select as $distrito)
                                            <option value="{{ $distrito->id }}">{{ $distrito->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('distrito_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Color *
                                    </label>
                                    <div class="flex items-center space-x-2">
                                        <input type="color" 
                                               wire:model="color"
                                               class="w-12 h-10 border border-gray-300 rounded-md cursor-pointer">
                                        <input type="text" 
                                               wire:model="color"
                                               class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                               pattern="#[0-9A-Fa-f]{6}">
                                    </div>
                                    @error('color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Zona
                                </label>
                                <select wire:model="zona_id"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Seleccione zona</option>
                                    @foreach($zonas_para_select as $zona)
                                        <option value="{{ $zona->id }}">{{ $zona->nombre }} ({{ $zona->distrito->nombre ?? 'Sin distrito' }})</option>
                                    @endforeach
                                </select>
                                @error('zona_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Latitud
                                    </label>
                                    <input type="number" 
                                           step="any"
                                           wire:model="latitud"
                                           placeholder="-25.2637"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('latitud') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">
                                        Longitud
                                    </label>
                                    <input type="number" 
                                           step="any"
                                           wire:model="longitud"
                                           placeholder="-57.5759"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    @error('longitud') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        @endif

                        <!-- Estado activo -->
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   id="activo"
                                   wire:model="activo"
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="activo" class="ml-2 block text-sm text-gray-900">
                                Estado activo
                            </label>
                        </div>
                    </div>

                    <!-- Modal buttons -->
                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button" 
                                wire:click="cerrarModal"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancelar
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            {{ $modo === 'crear' ? 'Crear' : 'Actualizar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
