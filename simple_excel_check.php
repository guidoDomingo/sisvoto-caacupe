<?php
ini_set('memory_limit', '512M');
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

try {
    $archivo = 'CAACUPE ANR 2026.xlsx';
    
    echo "📊 Analizando archivo Excel: $archivo\n\n";
    
    // Leer solo primera hoja, primeras 20 filas y columnas
    $reader = IOFactory::createReader('Xlsx');
    $reader->setReadDataOnly(true);
    
    $spreadsheet = $reader->load($archivo);
    $worksheet = $spreadsheet->getActiveSheet();
    
    echo "📋 Nombre de hoja: " . $worksheet->getTitle() . "\n\n";
    
    echo "🔍 PRIMERAS 20 FILAS Y COLUMNAS:\n";
    echo "================================\n";
    
    // Leer área específica
    $data = $worksheet->rangeToArray('A1:T20', null, true, false);
    
    foreach ($data as $rowIndex => $row) {
        $filaNumero = $rowIndex + 1;
        echo "Fila $filaNumero: ";
        
        $cellsWithData = [];
        foreach ($row as $colIndex => $cell) {
            if (!empty(trim($cell))) {
                $colLetter = chr(65 + $colIndex); // A=65
                $cellsWithData[] = "[$colLetter]" . substr(trim($cell), 0, 30) . (strlen(trim($cell)) > 30 ? '...' : '');
            }
        }
        
        if (!empty($cellsWithData)) {
            echo implode(' | ', $cellsWithData);
        } else {
            echo "(vacía)";
        }
        echo "\n";
        
        // Solo mostrar las primeras 15 filas para no saturar
        if ($rowIndex >= 14) break;
    }
    
    echo "\n🎯 ANÁLISIS:\n";
    echo "===========\n";
    
    // Intentar identificar si hay encabezados en alguna fila
    for ($row = 0; $row < min(10, count($data)); $row++) {
        $possibleHeaders = 0;
        $totalCells = 0;
        
        foreach ($data[$row] as $cell) {
            if (!empty(trim($cell))) {
                $totalCells++;
                $cellLower = strtolower(trim($cell));
                
                // Palabras que indican encabezados
                $headerWords = ['numero_ced', 'apellido', 'nombre', 'ci', 'cedula', 'telefono', 'direccion', 'nroreg', 'mesa', 'orden', 'desc_dep'];
                
                foreach ($headerWords as $word) {
                    if (strpos($cellLower, $word) !== false) {
                        $possibleHeaders++;
                        break;
                    }
                }
            }
        }
        
        if ($totalCells > 3 && $possibleHeaders > 2) {
            echo "✅ La fila " . ($row + 1) . " parece contener encabezados ($possibleHeaders de $totalCells celdas)\n";
        }
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>