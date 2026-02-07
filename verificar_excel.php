<?php
ini_set('memory_limit', '512M');
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

// Filtro para leer solo las primeras filas
class PrimerasFilasFilter implements IReadFilter
{
    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool {
        return $row <= 10; // Solo primeras 10 filas
    }
}

try {
    $archivoExcel = 'CAACUPE ANR 2026.xlsx';
    
    if (!file_exists($archivoExcel)) {
        echo "❌ El archivo $archivoExcel no se encuentra en el directorio.\n";
        exit;
    }
    
    echo "📊 Analizando archivo: $archivoExcel\n\n";
    
    // Crear reader con filtro
    $reader = IOFactory::createReader('Xlsx');
    $reader->setReadDataOnly(true);
    $reader->setReadFilter(new PrimerasFilasFilter());
    
    // Cargar solo las primeras filas
    $spreadsheet = $reader->load($archivoExcel);
    $worksheet = $spreadsheet->getActiveSheet();
    
    // Obtener nombre de la hoja
    echo "📋 Hoja activa: " . $worksheet->getTitle() . "\n";
    
    // Obtener información del archivo completo
    echo "📏 Analizando primeras filas para detectar estructura...\n\n";
    
    // Mostrar encabezados (primera fila)
    echo "🔤 COLUMNAS DETECTADAS:\n";
    echo "==================\n";
    
    $encabezados = [];
    $columna = 'A';
    $indice = 1;
    
    while ($worksheet->getCell($columna . '1')->getValue() !== null) {
        $valor = $worksheet->getCell($columna . '1')->getValue();
        if (!empty(trim($valor))) {
            $encabezados[] = trim($valor);
            echo "$indice. " . trim($valor) . "\n";
            $indice++;
        }
        $columna++;
        if ($columna > 'Z') break; // Limitar a 26 columnas para evitar problemas
    }
    
    echo "\n📊 Columnas detectadas: " . count($encabezados) . "\n\n";
    
    // Mostrar algunas filas de ejemplo
    echo "🔎 DATOS DE EJEMPLO:\n";
    echo "==================\n";
    
    for ($fila = 2; $fila <= 4; $fila++) { // Mostrar filas 2, 3 y 4
        echo "Fila $fila:\n";
        
        foreach ($encabezados as $indice => $encabezado) {
            $columna = chr(65 + $indice); // A=65, B=66, etc.
            $valor = $worksheet->getCell($columna . $fila)->getValue();
            
            if (!empty(trim($valor))) {
                echo "  " . trim($encabezado) . ": " . trim($valor) . "\n";
            }
        }
        echo "\n";
    }
    
    // Verificar compatibilidad con formato TSJE
    echo "🔍 ANÁLISIS DE COMPATIBILIDAD:\n";
    echo "============================\n";
    
    $columnasTSJE = [
        'nroreg' => false,
        'numero_ced' => false,
        'cod_dpto' => false,
        'desc_dep' => false,
        'mesa' => false,
        'orden' => false,
        'apellido' => false,
        'nombre' => false
    ];
    
    foreach ($encabezados as $encabezado) {
        $encabezadoLower = strtolower(trim($encabezado));
        foreach ($columnasTSJE as $columnaTSJE => $encontrado) {
            if (strpos($encabezadoLower, $columnaTSJE) !== false) {
                $columnasTSJE[$columnaTSJE] = true;
            }
        }
    }
    
    $compatibles = array_sum($columnasTSJE);
    $total = count($columnasTSJE);
    
    echo "Columnas TSJE encontradas: $compatibles/$total\n";
    
    foreach ($columnasTSJE as $columna => $encontrado) {
        $estado = $encontrado ? "✅" : "❌";
        echo "$estado $columna\n";
    }
    
    if ($compatibles >= 4) {
        echo "\n🎉 ¡COMPATIBLE! Este archivo parece ser del formato TSJE.\n";
        echo "El sistema lo detectará automáticamente y lo procesará correctamente.\n";
    } else {
        echo "\n⚠️  El archivo no parece ser del formato TSJE estándar.\n";
        echo "Puede requerir mapeo manual de columnas.\n";
    }

} catch (Exception $e) {
    echo "❌ Error al procesar el archivo: " . $e->getMessage() . "\n";
}
?>