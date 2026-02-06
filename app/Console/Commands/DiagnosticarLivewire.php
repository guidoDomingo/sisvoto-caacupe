<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DiagnosticarLivewire extends Command
{
    protected $signature = 'diagnosticar:livewire';
    protected $description = 'Diagnostica problemas con Livewire en servidor remoto';

    public function handle()
    {
        $this->info('=== DIAGNÓSTICO LIVEWIRE ===');
        $this->newLine();

        // 1. Verificar archivos
        $this->info('1. Verificando archivos de Livewire:');
        $livewirePath = public_path('vendor/livewire');
        
        if (is_dir($livewirePath)) {
            $this->line("✓ Directorio existe: {$livewirePath}");
            
            $files = ['livewire.js', 'livewire.min.js', 'manifest.json'];
            foreach ($files as $file) {
                $filePath = $livewirePath . '/' . $file;
                if (file_exists($filePath)) {
                    $size = filesize($filePath);
                    $this->line("  ✓ {$file} (tamaño: {$size} bytes)");
                } else {
                    $this->error("  ❌ {$file} NO encontrado");
                }
            }
        } else {
            $this->error('❌ Directorio public/vendor/livewire NO existe');
            $this->warn('Ejecuta: php artisan livewire:publish --assets');
        }
        $this->newLine();

        // 2. Configuración
        $this->info('2. Configuración de Livewire:');
        $this->line('inject_assets: ' . (config('livewire.inject_assets', true) ? '✓ true' : '❌ false'));
        $this->line('app.url: ' . config('app.url'));
        $this->line('app.asset_url: ' . (config('app.asset_url') ?: 'null'));
        $this->newLine();

        // 3. Probar URLs
        $this->info('3. URLs que Livewire intenta cargar:');
        $baseUrl = rtrim(config('app.url'), '/');
        $urls = [
            $baseUrl . '/livewire/livewire.js',
            $baseUrl . '/vendor/livewire/livewire.js',
        ];

        foreach ($urls as $url) {
            $this->line("Probando: {$url}");
            
            // Intentar obtener headers
            $context = stream_context_create([
                'http' => [
                    'method' => 'HEAD',
                    'timeout' => 5,
                    'ignore_errors' => true
                ]
            ]);
            
            $result = @get_headers($url, 1, $context);
            if ($result && strpos($result[0], '200') !== false) {
                $this->line("  ✓ Accesible");
                if (isset($result['Content-Type'])) {
                    $this->line("  Content-Type: " . $result['Content-Type']);
                }
            } else {
                $this->error("  ❌ No accesible");
            }
        }
        $this->newLine();

        // 4. Recomendaciones
        $this->info('4. Posibles soluciones:');
        $this->line('a) En el servidor, ejecuta:');
        $this->line('   php artisan livewire:publish --assets');
        $this->line('   chmod -R 755 public/vendor/livewire/');
        $this->newLine();
        
        $this->line('b) Verifica configuración del servidor web (.htaccess):');
        $htaccessPath = public_path('.htaccess');
        if (file_exists($htaccessPath)) {
            $this->line("  ✓ .htaccess existe");
        } else {
            $this->error("  ❌ .htaccess NO existe - ejecuta: php artisan config:publish");
        }
        $this->newLine();
        
        $this->line('c) Si el problema persiste, agrega en .env:');
        $this->line('   ASSET_URL=' . config('app.url'));

        return 0;
    }
}