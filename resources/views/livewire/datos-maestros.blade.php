<div>
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Datos Maestros</h1>
        <p class="mt-1 text-sm text-gray-600">Gestione barrios, zonas y distritos para organizar votantes y planificar actividades</p>
    </div>

    <!-- Flash Messages -->
    @if (session()->has('message'))
        <div role="alert" class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded relative">
            {{ session('message') }}
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

    <!-- Content Area básico -->
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-center">
            <h3 class="text-lg font-medium text-gray-900 mb-2">
                Gestión de {{ ucfirst($tipoActivo) }}
            </h3>
            <p class="text-gray-500">
                Funcionalidad completa próximamente - Migraciones pendientes
            </p>
        </div>
    </div>
</div>
