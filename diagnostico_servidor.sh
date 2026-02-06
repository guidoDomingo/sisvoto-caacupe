#!/bin/bash

echo "=== DIAGNÓSTICO LIVEWIRE EN SERVIDOR REMOTO ==="
echo ""

# Verificar si los archivos de Livewire existen
echo "1. Verificando archivos de Livewire:"
if [ -d "public/vendor/livewire" ]; then
    echo "✓ Directorio public/vendor/livewire existe"
    echo "Archivos encontrados:"
    ls -la public/vendor/livewire/
    echo ""
    
    # Verificar permisos
    echo "2. Verificando permisos:"
    echo "Permisos del directorio:"
    ls -ld public/vendor/livewire/
    echo "Permisos de livewire.js:"
    if [ -f "public/vendor/livewire/livewire.js" ]; then
        ls -l public/vendor/livewire/livewire.js
    else
        echo "❌ livewire.js no encontrado"
    fi
    echo ""
else
    echo "❌ Directorio public/vendor/livewire NO existe"
    echo ""
fi

# Verificar configuración de Apache/Nginx
echo "3. Verificando configuración del servidor web:"
echo "Probando acceso directo al archivo JS:"
curl -I "https://gruizsystem.com/vendor/livewire/livewire.js" 2>/dev/null | head -5
echo ""

# Verificar variables de entorno relevantes
echo "4. Verificando configuración de Laravel:"
echo "APP_URL: $(grep APP_URL .env 2>/dev/null || echo 'No definido')"
echo "APP_ENV: $(grep APP_ENV .env 2>/dev/null || echo 'No definido')"
echo "ASSET_URL: $(grep ASSET_URL .env 2>/dev/null || echo 'No definido')"
echo ""

# Verificar configuración de Livewire
echo "5. Verificando configuración de Livewire:"
php artisan tinker --execute="
echo 'inject_assets: ' . (config('livewire.inject_assets') ? 'true' : 'false') . PHP_EOL;
echo 'app.asset_url: ' . (config('app.asset_url') ?: 'null') . PHP_EOL;
echo 'app.url: ' . config('app.url') . PHP_EOL;
"

echo ""
echo "=== FIN DEL DIAGNÓSTICO ==="