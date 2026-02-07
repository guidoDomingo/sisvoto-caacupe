<?php

namespace App\Services;

use App\Models\Votante;
use App\Models\Lider;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Servicio de importación masiva de votantes
 */
class VoterImportService
{
    private array $errores = [];
    private array $advertencias = [];
    private int $nuevos = 0;
    private int $actualizados = 0;
    private int $duplicados = 0;
    private int $fallidos = 0;

    /**
     * Importar votantes desde archivo CSV o XLSX
     *
     * @param string $filePath
     * @param int $liderAsignadoId
     * @param int $usuarioId
     * @param bool $actualizarDuplicados
     * @return array
     */
    public function importar(
        string $filePath,
        int $liderAsignadoId,
        int $usuarioId,
        bool $actualizarDuplicados = false
    ): array {
        $this->resetContadores();

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);

        try {
            if (in_array($extension, ['xlsx', 'xls'])) {
                $datos = $this->leerExcel($filePath);
            } elseif ($extension === 'csv') {
                $datos = $this->leerCSV($filePath);
            } else {
                return [
                    'exito' => false,
                    'error' => 'Formato de archivo no soportado. Use CSV, XLS o XLSX.',
                ];
            }

            $this->procesarDatos($datos, $liderAsignadoId, $usuarioId, $actualizarDuplicados);

            return [
                'exito' => true,
                'total_procesados' => count($datos),
                'nuevos' => $this->nuevos,
                'actualizados' => $this->actualizados,
                'duplicados' => $this->duplicados,
                'fallidos' => $this->fallidos,
                'errores' => $this->errores,
                'advertencias' => $this->advertencias,
            ];
        } catch (\Exception $e) {
            return [
                'exito' => false,
                'error' => 'Error al procesar archivo: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Leer archivo CSV
     *
     * @param string $filePath
     * @return array
     */
    private function leerCSV(string $filePath): array
    {
        $datos = [];
        $encabezados = [];

        if (($handle = fopen($filePath, 'r')) !== false) {
            $primeraFila = true;

            while (($fila = fgetcsv($handle, 1000, ',')) !== false) {
                if ($primeraFila) {
                    $encabezados = array_map('trim', $fila);
                    $primeraFila = false;
                    continue;
                }

                if (count($fila) === count($encabezados)) {
                    $datos[] = array_combine($encabezados, $fila);
                }
            }

            fclose($handle);
        }

        return $datos;
    }

    /**
     * Leer archivo Excel
     *
     * @param string $filePath
     * @return array
     */
    private function leerExcel(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $filas = $worksheet->toArray();

        if (empty($filas)) {
            return [];
        }

        // Detectar fila de encabezados automáticamente
        $filaEncabezados = $this->detectarFilaEncabezados($filas);
        
        if ($filaEncabezados === -1) {
            // Si no se detectan encabezados, asumir primera fila
            $encabezados = array_map('trim', array_shift($filas));
        } else {
            // Usar la fila detectada como encabezados
            $encabezados = array_map('trim', $filas[$filaEncabezados]);
            // Remover todas las filas anteriores a los datos
            $filas = array_slice($filas, $filaEncabezados + 1);
        }

        $datos = [];

        foreach ($filas as $fila) {
            if (count($fila) >= count($encabezados)) {
                // Tomar solo las columnas necesarias para evitar problemas con columnas extra
                $filaRecortada = array_slice($fila, 0, count($encabezados));
                
                // Verificar que la fila no esté vacía (al menos debe tener CI o teléfono)
                $esFilaValida = false;
                foreach ($filaRecortada as $celda) {
                    if (!empty(trim($celda)) && strlen(trim($celda)) > 2) {
                        $esFilaValida = true;
                        break;
                    }
                }
                
                if ($esFilaValida) {
                    $datos[] = array_combine($encabezados, $filaRecortada);
                }
            }
        }

        return $datos;
    }

    /**
     * Detectar automáticamente la fila que contiene los encabezados
     *
     * @param array $filas
     * @return int
     */
    private function detectarFilaEncabezados(array $filas): int
    {
        $encabezadosComunes = [
            'numero_ced', 'ci', 'cedula', 'apellido', 'nombre', 'telefono', 'celular',
            'direccion', 'email', 'fecha_naci', 'genero', 'sexo', 'nroreg'
        ];

        for ($i = 0; $i < min(5, count($filas)); $i++) {
            $fila = $filas[$i];
            $coincidencias = 0;
            
            foreach ($fila as $celda) {
                if (!empty($celda)) {
                    $celdaLower = strtolower(trim($celda));
                    foreach ($encabezadosComunes as $encabezado) {
                        if (strpos($celdaLower, $encabezado) !== false) {
                            $coincidencias++;
                            break;
                        }
                    }
                }
            }

            // Si encuentra al menos 3 encabezados conocidos, es probable que sea la fila correcta
            if ($coincidencias >= 3) {
                return $i;
            }
        }

        return -1; // No se detectó fila de encabezados
    }

    /**
     * Procesar datos y crear/actualizar votantes
     *
     * @param array $datos
     * @param int $liderAsignadoId
     * @param int $usuarioId
     * @param bool $actualizarDuplicados
     */
    private function procesarDatos(
        array $datos,
        int $liderAsignadoId,
        int $usuarioId,
        bool $actualizarDuplicados
    ): void {
        foreach ($datos as $index => $fila) {
            $numeroFila = $index + 2; // +2 porque empieza en 1 y saltamos encabezado

            try {
                $datosVotante = $this->mapearDatos($fila);
                
                // Validar datos
                $validacion = $this->validarDatos($datosVotante, $numeroFila);
                
                if (!$validacion['valido']) {
                    $this->fallidos++;
                    continue;
                }

                // Usar los datos potencialmente modificados por la validación
                $datosVotante = $validacion['datos'];

                // Verificar duplicados por CI o teléfono
                $existente = $this->buscarDuplicado($datosVotante);

                if ($existente) {
                    if ($actualizarDuplicados) {
                        $this->actualizarVotante($existente, $datosVotante, $usuarioId);
                        $this->actualizados++;
                    } else {
                        $this->duplicados++;
                        $this->advertencias[] = [
                            'fila' => $numeroFila,
                            'mensaje' => "Votante duplicado (CI: {$datosVotante['ci']})",
                        ];
                    }
                } else {
                    $this->crearVotante($datosVotante, $liderAsignadoId, $usuarioId);
                    $this->nuevos++;
                }
            } catch (\Exception $e) {
                $this->fallidos++;
                $this->errores[] = [
                    'fila' => $numeroFila,
                    'error' => $e->getMessage(),
                ];
            }
        }
    }

    /**
     * Mapear datos del archivo a campos del modelo
     *
     * @param array $fila
     * @return array
     */
    private function mapearDatos(array $fila): array
    {
        // Normalizar las claves del array para manejar diferentes formatos
        $filaNormalizada = [];
        foreach ($fila as $clave => $valor) {
            $claveNormalizada = strtolower(trim($clave));
            $filaNormalizada[$claveNormalizada] = $valor;
        }

        return [
            // Mapeo para Excel TSJE y formato ANR Caacupé
            'ci' => $filaNormalizada['numero_ced'] ?? $filaNormalizada['cedula'] ?? $filaNormalizada['ci'] ?? null,
            'nombres' => $filaNormalizada['nombre'] ?? $filaNormalizada['nombres'] ?? null,
            'apellidos' => $filaNormalizada['apellido'] ?? $filaNormalizada['apellidos'] ?? null,
            'direccion' => $filaNormalizada['direccion'] ?? null,
            'telefono' => $filaNormalizada['celular'] ?? $filaNormalizada['telefono'] ?? $filaNormalizada['tel'] ?? null,
            'genero' => $this->mapearGenero($filaNormalizada['sexo'] ?? $filaNormalizada['genero'] ?? null),
            'fecha_nacimiento' => $this->parsearFecha($filaNormalizada['fecha_naci'] ?? $filaNormalizada['fecha_nacimiento'] ?? $filaNormalizada['nacimiento'] ?? null),
            
            // Campos adicionales del Excel TSJE
            'nro_registro' => $filaNormalizada['nroreg'] ?? null,
            'codigo_departamento' => $filaNormalizada['cod_dpto'] ?? null,
            'departamento' => $filaNormalizada['desc_dep'] ?? null,
            'codigo_distrito' => $filaNormalizada['cod_dist'] ?? null,
            'distrito_tsje' => $filaNormalizada['desc_dis'] ?? $filaNormalizada['distrito'] ?? null,
            'codigo_seccion' => $filaNormalizada['codigo_sec'] ?? $filaNormalizada['secc'] ?? null,
            'seccion' => $filaNormalizada['desc_sec'] ?? $filaNormalizada['secc'] ?? null,
            'codigo_barrio' => $filaNormalizada['codigo_sec'] ?? null,
            'barrio_tsje' => $filaNormalizada['desc_sec'] ?? null,
            'local_votacion' => $filaNormalizada['slocal'] ?? null,
            'descripcion_local' => $filaNormalizada['desc_locanr'] ?? null,
            'mesa' => $filaNormalizada['mesa'] ?? null,
            'orden' => $filaNormalizada['orden'] ?? null,
            'fecha_afiliacion' => $this->parsearFecha($filaNormalizada['fecha_afil'] ?? $filaNormalizada['a.afil'] ?? null),
            
            // Campos estándar (compatibilidad con otros formatos)
            'email' => $filaNormalizada['email'] ?? $filaNormalizada['correo'] ?? null,
            'barrio' => $filaNormalizada['barrio'] ?? null,
            'zona' => $filaNormalizada['zona'] ?? null,
            'ocupacion' => $filaNormalizada['ocupacion'] ?? $filaNormalizada['abogado'] ?? null,
            'codigo_intencion' => strtoupper($filaNormalizada['codigo_intencion'] ?? $filaNormalizada['intencion'] ?? 'C'),
            'necesita_transporte' => $this->parsearBooleano($filaNormalizada['necesita_transporte'] ?? $filaNormalizada['transporte'] ?? false),
            'notas' => $this->construirNotasExtendidas($filaNormalizada),
            'latitud' => $filaNormalizada['latitud'] ?? $filaNormalizada['lat'] ?? null,
            'longitud' => $filaNormalizada['longitud'] ?? $filaNormalizada['lon'] ?? $filaNormalizada['lng'] ?? null,
        ];
    }

    /**
     * Mapear género de diferentes formatos
     */
    private function mapearGenero($valor): ?string
    {
        if (empty($valor)) {
            return null;
        }

        $valorLower = strtolower(trim($valor));
        
        switch ($valorLower) {
            case 'm':
            case 'masculino':
            case 'male':
                return 'M';
            case 'f':
            case 'femenino':
            case 'female':
                return 'F';
            default:
                return strtoupper(substr($valor, 0, 1)); // Primera letra en mayúscula
        }
    }

    /**
     * Construir notas extendidas con información adicional del Excel
     */
    private function construirNotasExtendidas(array $filaNormalizada): ?string
    {
        $notas = [];
        
        // Notas explícitas del usuario
        if (!empty($filaNormalizada['notas'])) {
            $notas[] = trim($filaNormalizada['notas']);
        }

        // Información adicional del Excel ANR Caacupé
        $infoAdicional = [];
        
        if (!empty($filaNormalizada['partido'])) {
            $infoAdicional[] = "Partido: " . trim($filaNormalizada['partido']);
        }

        if (!empty($filaNormalizada['gral2021'])) {
            $infoAdicional[] = "Gral2021: " . trim($filaNormalizada['gral2021']);
        }

        if (!empty($filaNormalizada['anr2022'])) {
            $infoAdicional[] = "ANR2022: " . trim($filaNormalizada['anr2022']);
        }

        if (!empty($filaNormalizada['gral2023'])) {
            $infoAdicional[] = "Gral2023: " . trim($filaNormalizada['gral2023']);
        }

        if (!empty($filaNormalizada['func_publicos'])) {
            $infoAdicional[] = "Func. Públicos: " . trim($filaNormalizada['func_publicos']);
        }

        if (!empty($filaNormalizada['jubilados'])) {
            $infoAdicional[] = "Jubilado: " . trim($filaNormalizada['jubilados']);
        }

        if (!empty($filaNormalizada['abogado'])) {
            $infoAdicional[] = "Profesión: " . trim($filaNormalizada['abogado']);
        }

        if (!empty($infoAdicional)) {
            $notas[] = "Info adicional: " . implode(' | ', $infoAdicional);
        }

        return !empty($notas) ? implode("\n", $notas) : null;
    }

    /**
     * Validar datos del votante
     *
     * @param array $datos
     * @param int $numeroFila
     * @return array
     */
    private function validarDatos(array $datos, int $numeroFila): array
    {
        // Para archivos TSJE, el CI es obligatorio pero nombres/apellidos pueden estar vacios inicialmente
        $esFormatoTSJE = !empty($datos['nro_registro']) || !empty($datos['mesa']) || !empty($datos['orden']);
        
        if ($esFormatoTSJE) {
            $reglas = [
                'ci' => 'required|string|max:20',
                'nombres' => 'nullable|string|max:100',
                'apellidos' => 'nullable|string|max:100',
                'telefono' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:100',
                'codigo_intencion' => 'nullable|in:A,B,C,D,E',
            ];
            
            // Si nombres o apellidos están vacíos, asignar valores por defecto
            if (empty($datos['nombres'])) {
                $datos['nombres'] = 'PENDIENTE';
            }
            if (empty($datos['apellidos'])) {
                $datos['apellidos'] = 'PENDIENTE';
            }
        } else {
            $reglas = [
                'ci' => 'nullable|string|max:20',
                'nombres' => 'required|string|max:100',
                'apellidos' => 'required|string|max:100',
                'telefono' => 'nullable|string|max:20',
                'email' => 'nullable|email|max:100',
                'codigo_intencion' => 'nullable|in:A,B,C,D,E',
            ];
        }

        $validator = Validator::make($datos, $reglas);

        if ($validator->fails()) {
            $this->errores[] = [
                'fila' => $numeroFila,
                'errores' => $validator->errors()->all(),
            ];

            return ['valido' => false, 'datos' => $datos];
        }

        return ['valido' => true, 'datos' => $datos];
    }

    /**
     * Buscar votante duplicado por CI o teléfono
     *
     * @param array $datos
     * @return Votante|null
     */
    private function buscarDuplicado(array $datos): ?Votante
    {
        if ($datos['ci']) {
            $votante = Votante::where('ci', $datos['ci'])->first();
            if ($votante) {
                return $votante;
            }
        }

        if ($datos['telefono']) {
            return Votante::where('telefono', $datos['telefono'])->first();
        }

        return null;
    }

    /**
     * Crear nuevo votante
     *
     * @param array $datos
     * @param int $liderAsignadoId
     * @param int $usuarioId
     */
    private function crearVotante(array $datos, int $liderAsignadoId, int $usuarioId): void
    {
        $datos['lider_asignado_id'] = $liderAsignadoId;
        $datos['creado_por_usuario_id'] = $usuarioId;
        $datos['actualizado_por_usuario_id'] = $usuarioId;
        $datos['estado_contacto'] = 'Nuevo';

        Votante::create($datos);
    }

    /**
     * Actualizar votante existente
     *
     * @param Votante $votante
     * @param array $datos
     * @param int $usuarioId
     */
    private function actualizarVotante(Votante $votante, array $datos, int $usuarioId): void
    {
        $datos['actualizado_por_usuario_id'] = $usuarioId;
        unset($datos['ci']); // No actualizar CI

        $votante->update($datos);
    }

    /**
     * Parsear valor booleano desde string
     *
     * @param mixed $valor
     * @return bool
     */
    private function parsearBooleano($valor): bool
    {
        if (is_bool($valor)) {
            return $valor;
        }

        $valor = strtolower(trim($valor));
        return in_array($valor, ['si', 'sí', 'yes', 'true', '1', 'verdadero']);
    }

    /**
     * Parsear fecha desde diferentes formatos
     *
     * @param mixed $valor
     * @return string|null
     */
    private function parsearFecha($valor): ?string
    {
        if (empty($valor)) {
            return null;
        }

        // Si ya es una fecha válida, devolverla
        if ($valor instanceof \DateTime) {
            return $valor->format('Y-m-d');
        }

        $valor = trim($valor);
        
        // Si es un número (formato serial de Excel), convertirlo
        if (is_numeric($valor)) {
            try {
                // Excel usa 1900-01-01 como fecha base (número serial 1)
                // Pero tiene un bug donde trata 1900 como año bisiesto
                $baseDate = new \DateTime('1900-01-01');
                $days = intval($valor) - 1; // Restar 1 porque Excel cuenta desde 1
                
                // Ajustar por el bug de Excel del año 1900
                if ($valor > 59) {
                    $days -= 1;
                }
                
                $fecha = clone $baseDate;
                $fecha->add(new \DateInterval("P{$days}D"));
                
                return $fecha->format('Y-m-d');
            } catch (\Exception $e) {
                // Si falla la conversión, continúar con otros métodos
            }
        }
        
        // Intentar diferentes formatos comunes
        $formatos = [
            'd/m/Y',      // 26/12/1980
            'd-m-Y',      // 26-12-1980
            'Y-m-d',      // 1980-12-26
            'd/m/y',      // 26/12/80
            'd-m-y',      // 26-12-80
            'm/d/Y',      // 12/26/1980
            'Y/m/d',      // 1980/12/26
        ];

        foreach ($formatos as $formato) {
            $fecha = \DateTime::createFromFormat($formato, $valor);
            if ($fecha !== false) {
                return $fecha->format('Y-m-d');
            }
        }

        // Intentar con strtotime como último recurso
        $timestamp = strtotime($valor);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return null;
    }

    /**
     * Resetear contadores
     */
    private function resetContadores(): void
    {
        $this->errores = [];
        $this->advertencias = [];
        $this->nuevos = 0;
        $this->actualizados = 0;
        $this->duplicados = 0;
        $this->fallidos = 0;
    }

    /**
     * Generar plantilla CSV de ejemplo
     *
     * @return string Contenido del CSV
     */
    public static function generarPlantillaCSV(): string
    {
        $encabezados = [
            'ci',
            'nombres',
            'apellidos',
            'telefono',
            'email',
            'fecha_nacimiento',
            'genero',
            'ocupacion',
            'direccion',
            'barrio',
            'zona',
            'distrito',
            'latitud',
            'longitud',
            'codigo_intencion',
            'necesita_transporte',
            'notas',
        ];

        $ejemplo = [
            '1234567',
            'Juan',
            'Pérez García',
            '0981-123456',
            'juan@email.com',
            '1985-05-15',
            'M',
            'Comerciante',
            'Av. Principal 123',
            'Centro',
            'Zona 1',
            'Distrito 1',
            '-25.2867',
            '-57.6333',
            'A',
            'Si',
            'Votante comprometido',
        ];

        $csv = implode(',', $encabezados) . "\n";
        $csv .= implode(',', $ejemplo) . "\n";

        return $csv;
    }
}
