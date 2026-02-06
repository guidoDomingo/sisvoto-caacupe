<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <!-- Header -->
            <div class="bg-red-50 p-6 border-b border-red-200">
                <div class="flex items-center">
                    <svg class="w-8 h-8 text-red-600 mr-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <h1 class="text-2xl font-bold text-red-800">Limpieza de Datos del Sistema</h1>
                        <p class="text-red-600">⚠️ ZONA PELIGROSA: Las operaciones aquí son IRREVERSIBLES</p>
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            @if (session()->has('message'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3">
                    {{ session('message') }}
                </div>
            @endif

            @if (session()->has('error'))
                <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Estadísticas actuales -->
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Estado Actual del Sistema</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($estadisticas['votantes']) }}</div>
                        <div class="text-sm text-blue-600">Votantes</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-green-600">{{ number_format($estadisticas['contactos']) }}</div>
                        <div class="text-sm text-green-600">Contactos</div>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600">{{ number_format($estadisticas['viajes']) }}</div>
                        <div class="text-sm text-yellow-600">Viajes</div>
                    </div>
                    <div class="bg-purple-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-purple-600">{{ number_format($estadisticas['visitas']) }}</div>
                        <div class="text-sm text-purple-600">Visitas</div>
                    </div>
                    <div class="bg-indigo-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-indigo-600">{{ number_format($estadisticas['gastos']) }}</div>
                        <div class="text-sm text-indigo-600">Gastos</div>
                    </div>
                    <div class="bg-pink-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-pink-600">{{ number_format($estadisticas['usuarios']) }}</div>
                        <div class="text-sm text-pink-600">Usuarios</div>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-gray-600">{{ number_format($estadisticas['lideres']) }}</div>
                        <div class="text-sm text-gray-600">Líderes</div>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg">
                        <div class="text-2xl font-bold text-red-600">{{ number_format($estadisticas['auditorias']) }}</div>
                        <div class="text-sm text-red-600">Auditorías</div>
                    </div>
                </div>
            </div>

            <!-- Operaciones de limpieza -->
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Operaciones de Limpieza</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Limpieza parcial -->
                    <div class="border border-yellow-200 rounded-lg p-6">
                        <h4 class="text-md font-semibold text-yellow-800 mb-4">🟡 Limpieza Parcial</h4>
                        
                        <div class="space-y-3">
                            <button wire:click="abrirModal('votantes')" 
                                    class="w-full bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-4 py-3 rounded-lg text-left">
                                <div class="font-medium">Eliminar todos los votantes</div>
                                <div class="text-sm">Elimina votantes y sus contactos</div>
                            </button>
                            
                            <button wire:click="abrirModal('viajes')" 
                                    class="w-full bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-4 py-3 rounded-lg text-left">
                                <div class="font-medium">Eliminar todos los viajes</div>
                                <div class="text-sm">Elimina viajes y gastos asociados</div>
                            </button>
                            
                            <button wire:click="abrirModal('visitas')" 
                                    class="w-full bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-4 py-3 rounded-lg text-left">
                                <div class="font-medium">Eliminar todas las visitas</div>
                                <div class="text-sm">Elimina registro de visitas</div>
                            </button>
                            
                            <button wire:click="abrirModal('auditorias')" 
                                    class="w-full bg-yellow-100 hover:bg-yellow-200 text-yellow-800 px-4 py-3 rounded-lg text-left">
                                <div class="font-medium">Eliminar auditorías</div>
                                <div class="text-sm">Limpia logs de auditoría</div>
                            </button>
                        </div>
                    </div>

                    <!-- Reseteo completo -->
                    <div class="border border-red-300 rounded-lg p-6">
                        <h4 class="text-md font-semibold text-red-800 mb-4">🔴 Reseteo Completo</h4>
                        
                        <div class="bg-red-50 p-4 rounded mb-4">
                            <p class="text-sm text-red-700 mb-2"><strong>⚠️ ATENCIÓN:</strong></p>
                            <p class="text-sm text-red-600">Esta operación eliminará TODOS los datos del sistema excepto tu usuario administrador.</p>
                        </div>
                        
                        <button wire:click="abrirModal('completo')" 
                                class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-3 rounded-lg">
                            <div class="font-medium">🗑️ RESETEAR SISTEMA COMPLETO</div>
                            <div class="text-sm">Elimina TODOS los datos</div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
            <div class="relative mx-auto p-6 border max-w-lg w-full shadow-lg rounded-lg bg-white">
                <div class="mt-3">
                    <!-- Header del modal -->
                    <div class="flex justify-between items-center pb-3 border-b">
                        <h3 class="text-lg font-medium text-red-900">
                            ⚠️ Confirmar Operación Peligrosa
                        </h3>
                        <button wire:click="cerrarModal" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="mt-4">
                        <div class="bg-red-50 border border-red-200 rounded p-4 mb-4">
                            <p class="text-red-800 font-medium mb-2">
                                @if($operacionSeleccionada === 'votantes')
                                    Vas a eliminar TODOS los votantes y sus contactos
                                @elseif($operacionSeleccionada === 'viajes')
                                    Vas a eliminar TODOS los viajes y gastos
                                @elseif($operacionSeleccionada === 'visitas')
                                    Vas a eliminar TODAS las visitas
                                @elseif($operacionSeleccionada === 'auditorias')
                                    Vas a eliminar TODAS las auditorías
                                @elseif($operacionSeleccionada === 'completo')
                                    Vas a RESETEAR COMPLETAMENTE el sistema
                                @endif
                            </p>
                            <p class="text-red-600 text-sm">Esta operación es IRREVERSIBLE.</p>
                        </div>

                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Para confirmar, escribe exactamente:
                                <span class="font-bold text-red-600">
                                    @if($operacionSeleccionada === 'votantes')
                                        "ELIMINAR VOTANTES"
                                    @elseif($operacionSeleccionada === 'viajes')
                                        "ELIMINAR VIAJES"
                                    @elseif($operacionSeleccionada === 'visitas')
                                        "ELIMINAR VISITAS"
                                    @elseif($operacionSeleccionada === 'auditorias')
                                        "ELIMINAR AUDITORIAS"
                                    @elseif($operacionSeleccionada === 'completo')
                                        "RESETEAR SISTEMA COMPLETO"
                                    @endif
                                </span>
                            </label>
                            <input type="text" wire:model="confirmacion" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-red-500"
                                   placeholder="Escribir confirmación...">
                        </div>

                        <div class="flex gap-3">
                            <button wire:click="cerrarModal" 
                                    class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 px-4 py-2 rounded-lg">
                                Cancelar
                            </button>
                            <button wire:click="
                                @if($operacionSeleccionada === 'votantes') limpiarVotantes
                                @elseif($operacionSeleccionada === 'viajes') limpiarViajes
                                @elseif($operacionSeleccionada === 'visitas') limpiarVisitas
                                @elseif($operacionSeleccionada === 'auditorias') limpiarAuditorias
                                @elseif($operacionSeleccionada === 'completo') resetearCompleto
                                @endif
                            " class="flex-1 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg">
                                Confirmar Eliminación
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
