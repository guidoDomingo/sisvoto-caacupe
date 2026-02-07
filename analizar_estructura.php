<?php
ini_set('memory_limit', '512M');
require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\IReadFilter;

// Filtro para leer solo las primeras filas y columnas
class AreaLimitadaFilter implements IReadFilter
{
    public function readCell(string $columnAddress, int $row, string $worksheetName = ''): bool {
        $column = filter_var($columnAddress, FILTER_SANITIZE_STRING);
        $columnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($column);
        return $row <= 15 && $columnIndex <= 20; // Primeras 15 filas y 20 columnas
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
    $reader->setReadFilter(new AreaLimitadaFilter());
    
    // Cargar solo las primeras filas
    $spreadsheet = $reader->load($archivoExcel);
    $worksheet = $spreadsheet->getActiveSheet();
    
    echo "📋 Hoja activa: " . $worksheet->getTitle() . "\n\n";
    
    // Mostrar matriz de datos sin asumir formato
    echo "🔍 ESTRUCTURA COMPLETA (15x20):\n";
    echo "======================================\n";
    
    for ($fila = 1; $fila <= 15; $fila++) {
        echo "Fila $fila: ";
        $hayDatos = false;
        
        for ($col = 1; $col <= 20; $col++) {
            $columnaLetra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $valor = $worksheet->getCell($columnaLetra . $fila)->getValue();
            
            if (!empty(trim($valor))) {
                echo "[$columnaLetra]" . trim($valor) . " ";
                $hayDatos = true;
            }
        }
        
        if (!$hayDatos) {
            echo "(vacía)";
        }
        echo "\n";
    }
    
    echo "\n🔍 BÚSQUEDA DE ENCABEZADOS POTENCIALES:\n";
    echo "=====================================\n";
    
    $encabezadosEncontrados = [];
    
    // Buscar en las primeras 10 filas patrones de encabezados
    for ($fila = 1; $fila <= 10; $fila++) {
        for ($col = 1; $col <= 20; $col++) {
            $columnaLetra = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
            $valor = $worksheet->getCell($columnaLetra . $fila)->getValue();
            
            if (!empty(trim($valor))) {
                $valorStr = strtolower(trim($valor));
                
                // Típicos encabezados del TSJE o de votantes
                $posiblesEncabezados = [
                    'numero_ced', 'nroreg', 'apellido', 'nombre', 'ci', 'cedula',
                    'cod_dpto', 'desc_dep', 'distrito', 'mesa', 'orden', 
                    'telefono', 'email', 'direccion', 'fecha_naci'
                ];
                
                foreach ($posiblesEncabezados as $posibleEncabezado) {
                    if (stripos($valorStr, $posibleEncabezado) !== false) {
                        $encabezadosEncontrados[] = [
                            'posicion' => $columnaLetra . $fila,
                            'valor' => trim($valor),
                            'tipo' => $posibleEncabezado
                        ];
                    }
                }
            }
        }
    }
    
    if (!empty($encabezadosEncontrados)) {
        echo "Encabezados potenciales encontrados:\n";
        foreach ($encabezadosEncontrados as $encabezado) {
            echo "  {$encabezado['posicion']}: {$encabezado['valor']} ({$encabezado['tipo']})\n";
        }
    } else {
        echo "No se encontraron encabezados típicos del TSJE.\n";
        echo "El archivo puede tener un formato personalizado.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error al procesar el archivo: " . $e->getMessage() . "\n";
}
?>