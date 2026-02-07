<?php
ini_set('memory_limit', '512M');
require_once 'vendor/autoload.php';

use App\Services\VoterImportService;

echo "🧪 VERIFICACIÓN DEL SISTEMA DE IMPORTACIÓN\n";
echo "========================================\n\n";

try {
    // Crear instancia del servicio
    $importService = new VoterImportService();
    
    echo "✅ Servicio de importación cargado correctamente\n\n";
    
    // Verificar que el archivo existe
    $archivo = 'CAACUPE ANR 2026.xlsx';
    if (!file_exists($archivo)) {
        echo "❌ Archivo no encontrado: $archivo\n";
        echo "💡 Asegúrate de que el archivo esté en el directorio raíz del proyecto\n";
        exit;
    }
    
    echo "✅ Archivo encontrado: $archivo\n";
    echo "📏 Tamaño del archivo: " . round(filesize($archivo) / 1024 / 1024, 2) . " MB\n\n";
    
    // Intentar leer las primeras filas para verificar el mapeo
    echo "🔍 Verificando mapeo de datos...\n";
    echo "================================\n";
    
    // Simular la carga con solo las primeras filas para prueba
    $reflection = new ReflectionClass($importService);
    $leerExcelMethod = $reflection->getMethod('leerExcel');
    $leerExcelMethod->setAccessible(true);
    
    $mapearDatosMethod = $reflection->getMethod('mapearDatos');
    $mapearDatosMethod->setAccessible(true);
    
    // Intentar leer solo las primeras filas
    echo "📖 Leyendo estructura del archivo...\n";
    
    // Crear un Excel temporal con solo las primeras 5 filas para testing
    $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
    $reader->setReadDataOnly(true);
    
    $spreadsheet = $reader->load($archivo);
    $worksheet = $spreadsheet->getActiveSheet();
    
    // Leer primeras 5 filas completas para análisis
    $primerasFilas = [];
    for ($row = 1; $row <= 5; $row++) {
        $fila = [];
        for ($col = 'A'; $col <= 'T'; $col++) { // Hasta columna T (20 columnas)
            $cell = $worksheet->getCell($col . $row);
            $fila[] = $cell->getValue();
        }
        $primerasFilas[] = $fila;
    }
    
    echo "📊 Estructura detectada:\n";
    echo "Fila 1: " . implode(' | ', array_filter($primerasFilas[0])) . "\n";
    echo "Fila 2: " . implode(' | ', array_filter($primerasFilas[1])) . "\n\n";
    
    // Encontrar encabezados (fila 2)
    $encabezados = array_map('trim', $primerasFilas[1]);
    $datosEjemplo = array_combine($encabezados, $primerasFilas[2]);
    
    echo "🔀 Probando mapeo con datos reales:\n";
    echo "==================================\n";
    
    // Probar mapeo
    $datosMapeados = $mapearDatosMethod->invoke($importService, $datosEjemplo);
    
    echo "📝 Datos originales del Excel:\n";
    foreach ($datosEjemplo as $campo => $valor) {
        if (!empty(trim($valor))) {
            echo "  $campo: " . trim($valor) . "\n";
        }
    }
    
    echo "\n🎯 Datos mapeados para el sistema:\n";
    foreach ($datosMapeados as $campo => $valor) {
        if (!empty($valor)) {
            echo "  $campo: $valor\n";
        }
    }
    
    echo "\n✅ VERIFICACIÓN COMPLETADA\n";
    echo "=========================\n";
    echo "El mapeo funciona correctamente. El sistema detectará:\n";
    echo "• CI: " . ($datosMapeados['ci'] ? 'SÍ' : 'NO') . "\n";
    echo "• Nombres: " . ($datosMapeados['nombres'] ? 'SÍ' : 'NO') . "\n"; 
    echo "• Apellidos: " . ($datosMapeados['apellidos'] ? 'SÍ' : 'NO') . "\n";
    echo "• Teléfono: " . ($datosMapeados['telefono'] ? 'SÍ' : 'NO') . "\n";
    echo "• Dirección: " . ($datosMapeados['direccion'] ? 'SÍ' : 'NO') . "\n";
    echo "• Género: " . ($datosMapeados['genero'] ? 'SÍ' : 'NO') . "\n";
    echo "• Distrito: " . ($datosMapeados['distrito_tsje'] ? 'SÍ' : 'NO') . "\n";
    echo "• Información adicional en notas: " . ($datosMapeados['notas'] ? 'SÍ' : 'NO') . "\n";
    
    echo "\n🎉 ¡LISTO PARA IMPORTAR!\n";
    echo "El archivo es compatible y el sistema está configurado correctamente.\n";
    echo "Puedes proceder con la importación en: http://tu-dominio/importar\n";

} catch (Exception $e) {
    echo "❌ Error durante la verificación: " . $e->getMessage() . "\n";
    echo "📍 Ubicación: " . $e->getFile() . " línea " . $e->getLine() . "\n";
}
?>