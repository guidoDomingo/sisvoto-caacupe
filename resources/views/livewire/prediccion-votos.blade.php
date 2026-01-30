<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Predicción de Votos</h1>
        <p class="mt-1 text-sm text-gray-600">Calcule predicciones usando diferentes modelos estadísticos</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('error'))
        <div role="alert" class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded relative">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Configuration Panel -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6 sticky top-4">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración</h3>

                <div class="space-y-4">
                    <!-- Modelo -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Modelo de Predicción</label>
                        <select wire:model="modelo" 
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="heuristico">Heurístico (Probabilidades fijas)</option>
                            <option value="montecarlo">Monte Carlo (Simulación)</option>
                            <option value="combinado">Comparación Combinada</option>
                        </select>
                    </div>

                    <!-- Iteraciones (solo para Monte Carlo) -->
                    @if($modelo === 'montecarlo' || $modelo === 'combinado')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Iteraciones
                                <span class="text-xs text-gray-500">({{ number_format($iteraciones) }})</span>
                            </label>
                            <input wire:model="iteraciones" type="range" min="100" max="10000" step="100" 
                                class="w-full">
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>100</span>
                                <span>10,000</span>
                            </div>
                        </div>
                    @endif

                    <!-- Filtros -->
                    <div class="border-t pt-4">
                        <h4 class="text-sm font-medium text-gray-700 mb-3">Filtros (Opcional)</h4>

                        @if(!Auth::user()->hasRole('Líder'))
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-gray-600 mb-1">Líder</label>
                                <select wire:model="lider_id" 
                                    class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Todos los líderes</option>
                                    @foreach($lideres as $lider)
                                        <option value="{{ $lider->id }}">{{ $lider->usuario->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Barrio</label>
                            <input wire:model="barrio" type="text" 
                                class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Ej: Centro">
                        </div>

                        <div class="mb-3">
                            <label class="block text-xs font-medium text-gray-600 mb-1">Zona</label>
                            <input wire:model="zona" type="text" 
                                class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Ej: Zona 1">
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Distrito</label>
                            <input wire:model="distrito" type="text" 
                                class="w-full text-sm border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                placeholder="Ej: Distrito 1">
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex gap-2">
                        <button wire:click="calcular" 
                                :disabled="cargando"
                                class="flex-1 inline-flex justify-center items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg disabled:opacity-50">
                            @if($cargando)
                                <svg class="animate-spin h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Calculando...
                            @else
                                Calcular
                            @endif
                        </button>

                        <button wire:click="limpiar" 
                                class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            Limpiar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Results Panel -->
        <div class="lg:col-span-2">
            @if($resultado)
                @if($modelo === 'combinado')
                    <!-- Comparación Combinada -->
                    <div class="space-y-6">
                        <!-- Heurístico -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Predicción Heurística</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-blue-50 rounded-lg p-4">
                                    <div class="text-sm text-gray-600 mb-1">Votos Estimados</div>
                                    <div class="text-3xl font-bold text-blue-600">
                                        {{ number_format($resultado['heuristico']['votos_estimados'], 1) }}
                                    </div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="text-sm text-gray-600 mb-1">Porcentaje</div>
                                    <div class="text-3xl font-bold text-gray-900">
                                        {{ number_format($resultado['heuristico']['porcentaje_estimado'], 1) }}%
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Monte Carlo -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Predicción Monte Carlo</h3>
                            <div class="grid grid-cols-3 gap-4 mb-4">
                                <div class="bg-green-50 rounded-lg p-4">
                                    <div class="text-xs text-gray-600 mb-1">Media</div>
                                    <div class="text-2xl font-bold text-green-600">
                                        {{ number_format($resultado['montecarlo']['estadisticas']['media'], 1) }}
                                    </div>
                                </div>
                                <div class="bg-yellow-50 rounded-lg p-4">
                                    <div class="text-xs text-gray-600 mb-1">Mediana</div>
                                    <div class="text-2xl font-bold text-yellow-600">
                                        {{ $resultado['montecarlo']['estadisticas']['mediana'] }}
                                    </div>
                                </div>
                                <div class="bg-purple-50 rounded-lg p-4">
                                    <div class="text-xs text-gray-600 mb-1">Desv. Estándar</div>
                                    <div class="text-2xl font-bold text-purple-600">
                                        {{ number_format($resultado['montecarlo']['estadisticas']['desviacion_estandar'], 2) }}
                                    </div>
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="text-sm font-medium text-gray-700 mb-2">Intervalo de Confianza 80%</div>
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-semibold">
                                        {{ $resultado['montecarlo']['estadisticas']['p10'] }}
                                    </span>
                                    <span class="text-gray-400">→</span>
                                    <span class="text-lg font-semibold">
                                        {{ $resultado['montecarlo']['estadisticas']['p90'] }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Comparación -->
                        <div class="bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg shadow p-6 text-white">
                            <h3 class="text-lg font-semibold mb-4">Comparación</h3>
                            <div class="flex items-center justify-around">
                                <div class="text-center">
                                    <div class="text-sm opacity-90 mb-1">Diferencia Absoluta</div>
                                    <div class="text-3xl font-bold">
                                        {{ number_format($resultado['comparacion']['diferencia_absoluta'], 2) }}
                                    </div>
                                </div>
                                <div class="text-center">
                                    <div class="text-sm opacity-90 mb-1">Diferencia Porcentual</div>
                                    <div class="text-3xl font-bold">
                                        {{ number_format($resultado['comparacion']['diferencia_porcentual'], 2) }}%
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <!-- Single Model Result -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Resultado de la Predicción</h3>

                        <!-- Summary Cards -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="text-sm text-gray-600 mb-1">Total Votantes</div>
                                <div class="text-2xl font-bold text-gray-900">{{ $resultado['total_votantes'] }}</div>
                            </div>
                            
                            @if($modelo === 'heuristico')
                                <div class="bg-green-50 rounded-lg p-4">
                                    <div class="text-sm text-gray-600 mb-1">Votos Estimados</div>
                                    <div class="text-2xl font-bold text-green-600">
                                        {{ number_format($resultado['votos_estimados'], 1) }}
                                    </div>
                                </div>
                                <div class="bg-purple-50 rounded-lg p-4">
                                    <div class="text-sm text-gray-600 mb-1">Porcentaje</div>
                                    <div class="text-2xl font-bold text-purple-600">
                                        {{ number_format($resultado['porcentaje_estimado'], 1) }}%
                                    </div>
                                </div>
                            @else
                                <div class="bg-green-50 rounded-lg p-4">
                                    <div class="text-sm text-gray-600 mb-1">Media</div>
                                    <div class="text-2xl font-bold text-green-600">
                                        {{ number_format($resultado['estadisticas']['media'], 1) }}
                                    </div>
                                </div>
                                <div class="bg-yellow-50 rounded-lg p-4">
                                    <div class="text-sm text-gray-600 mb-1">Mediana</div>
                                    <div class="text-2xl font-bold text-yellow-600">
                                        {{ $resultado['estadisticas']['mediana'] }}
                                    </div>
                                </div>
                                <div class="bg-purple-50 rounded-lg p-4">
                                    <div class="text-sm text-gray-600 mb-1">Desv. Estándar</div>
                                    <div class="text-2xl font-bold text-purple-600">
                                        {{ number_format($resultado['estadisticas']['desviacion_estandar'], 2) }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Detailed Results -->
                        @if($modelo === 'montecarlo')
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-700 mb-3">Rango de Resultados</h4>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm text-gray-600">Mínimo</span>
                                        <span class="font-semibold">{{ $resultado['estadisticas']['min'] }}</span>
                                    </div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm text-gray-600">Percentil 10</span>
                                        <span class="font-semibold">{{ $resultado['estadisticas']['p10'] }}</span>
                                    </div>
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-sm text-gray-600">Percentil 90</span>
                                        <span class="font-semibold">{{ $resultado['estadisticas']['p90'] }}</span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-gray-600">Máximo</span>
                                        <span class="font-semibold">{{ $resultado['estadisticas']['max'] }}</span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Distribution by Intention -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 mb-3">Distribución por Intención de Voto</h4>
                            <div class="space-y-2">
                                @php
                                    $colores = ['A' => 'bg-green-500', 'B' => 'bg-blue-500', 'C' => 'bg-yellow-500', 'D' => 'bg-orange-500', 'E' => 'bg-red-500'];
                                    $porIntencion = $resultado['por_intencion'] ?? [];
                                @endphp
                                @foreach($porIntencion as $codigo => $cantidad)
                                    <div>
                                        <div class="flex items-center justify-between mb-1">
                                            <span class="text-sm font-medium text-gray-700">{{ $codigo }}</span>
                                            <span class="text-sm text-gray-600">{{ $cantidad }}</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div class="{{ $colores[$codigo] }} h-2 rounded-full" 
                                                 style="width: {{ $resultado['total_votantes'] > 0 ? ($cantidad / $resultado['total_votantes']) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <!-- Empty State -->
                <div class="bg-white rounded-lg shadow p-12 text-center">
                    <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">Sin resultados</h3>
                    <p class="mt-2 text-sm text-gray-500">Configure los parámetros y haga clic en "Calcular" para ver la predicción</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Sección Informativa -->
    <div class="mt-8 bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                ¿Cómo funcionan las predicciones?
            </h3>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Modelos de Predicción -->
                <div>
                    <h4 class="text-base font-semibold text-gray-900 mb-3">📊 Modelos de Predicción</h4>
                    
                    <div class="space-y-4">
                        <div class="border-l-4 border-blue-500 pl-4">
                            <h5 class="font-medium text-gray-900">Heurístico (Probabilidades fijas)</h5>
                            <p class="text-sm text-gray-600 mt-1">
                                Calcula votos usando probabilidades fijas basadas en los códigos de intención:
                                <strong>A=95%</strong>, <strong>B=75%</strong>, <strong>C=50%</strong>, <strong>D=25%</strong>, <strong>E=5%</strong>
                            </p>
                        </div>
                        
                        <div class="border-l-4 border-green-500 pl-4">
                            <h5 class="font-medium text-gray-900">Monte Carlo (Simulación)</h5>
                            <p class="text-sm text-gray-600 mt-1">
                                Realiza miles de simulaciones aleatorias considerando factores como abstención, 
                                cambios de última hora, y variabilidad en los códigos de intención.
                            </p>
                        </div>
                        
                        <div class="border-l-4 border-purple-500 pl-4">
                            <h5 class="font-medium text-gray-900">Comparación Combinada</h5>
                            <p class="text-sm text-gray-600 mt-1">
                                Muestra ambos métodos y calcula la diferencia entre ellos para evaluar 
                                la consistencia de las predicciones.
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Interpretación de Resultados -->
                <div>
                    <h4 class="text-base font-semibold text-gray-900 mb-3">📈 Interpretando los Resultados</h4>
                    
                    <div class="space-y-4">
                        <div class="bg-blue-50 rounded-lg p-3">
                            <h5 class="font-medium text-blue-900">Media vs Mediana</h5>
                            <p class="text-xs text-blue-700 mt-1">
                                <strong>Media:</strong> Promedio de todas las simulaciones<br>
                                <strong>Mediana:</strong> Valor central (50% de casos arriba/abajo)
                            </p>
                        </div>
                        
                        <div class="bg-yellow-50 rounded-lg p-3">
                            <h5 class="font-medium text-yellow-900">Desviación Estándar</h5>
                            <p class="text-xs text-yellow-700 mt-1">
                                Mide la variabilidad. <strong>Menor = más predecible</strong>. 
                                Valores típicos: 20-60 votos.
                            </p>
                        </div>
                        
                        <div class="bg-purple-50 rounded-lg p-3">
                            <h5 class="font-medium text-purple-900">Intervalo de Confianza 80%</h5>
                            <p class="text-xs text-purple-700 mt-1">
                                Rango donde caerán el 80% de los resultados posibles. 
                                <strong>Más estrecho = mayor certeza</strong>.
                            </p>
                        </div>
                        
                        <div class="bg-green-50 rounded-lg p-3">
                            <h5 class="font-medium text-green-900">Diferencia entre Modelos</h5>
                            <p class="text-xs text-green-700 mt-1">
                                <strong>&lt;2%:</strong> Predicciones muy consistentes<br>
                                <strong>2-5%:</strong> Consistentes<br>
                                <strong>&gt;5%:</strong> Revisar datos
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Factores Considerados -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <h4 class="text-base font-semibold text-gray-900 mb-3">⚙️ Factores Considerados en las Predicciones</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                    <div>
                        <h5 class="font-medium text-gray-900 mb-2">📋 Datos Base</h5>
                        <ul class="space-y-1">
                            <li>• Códigos de intención (A, B, C, D, E)</li>
                            <li>• Estado de contacto</li>
                            <li>• Historial de contactos</li>
                            <li>• Ubicación geográfica</li>
                        </ul>
                    </div>
                    <div>
                        <h5 class="font-medium text-gray-900 mb-2">🎯 Variables de Campaña</h5>
                        <ul class="space-y-1">
                            <li>• Efectividad por líder</li>
                            <li>• Densidad por zona</li>
                            <li>• Tiempo desde último contacto</li>
                            <li>• Necesidad de transporte</li>
                        </ul>
                    </div>
                    <div>
                        <h5 class="font-medium text-gray-900 mb-2">🔄 Factores de Incertidumbre</h5>
                        <ul class="space-y-1">
                            <li>• Abstención electoral</li>
                            <li>• Cambios de última hora</li>
                            <li>• Condiciones del día electoral</li>
                            <li>• Variabilidad estadística</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Recomendaciones -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4">
                    <h4 class="text-base font-semibold text-blue-900 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9.663 17h4.673a1.25 1.25 0 001.25-1.25v-2.5c0-.69.56-1.25 1.25-1.25a.75.75 0 000-1.5c-.69 0-1.25-.56-1.25-1.25v-2.5A1.25 1.25 0 0014.336 3H9.663a1.25 1.25 0 00-1.25 1.25v2.5C8.413 7.44 7.853 8 7.163 8a.75.75 0 000 1.5c.69 0 1.25.56 1.25 1.25v2.5A1.25 1.25 0 009.663 17zM12 10a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                        </svg>
                        💡 Recomendaciones de Uso
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-blue-800">
                        <div>
                            • <strong>Use "Comparación Combinada"</strong> para análisis completos<br>
                            • <strong>Actualice datos regularmente</strong> para mayor precisión<br>
                            • <strong>Compare intervalos de confianza</strong> para evaluar riesgo
                        </div>
                        <div>
                            • <strong>Filtre por zona/líder</strong> para estrategias específicas<br>
                            • <strong>Monitoree la desviación estándar</strong> como indicador de estabilidad<br>
                            • <strong>Use múltiples iteraciones</strong> (1000+) para mayor precisión
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
