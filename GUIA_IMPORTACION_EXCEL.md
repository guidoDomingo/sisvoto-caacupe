# 📋 Guía para Importar Votantes desde Excel

## ✅ Tu archivo está listo para importar

Tu archivo **"CAACUPE ANR 2026.xlsx"** es compatible con el sistema de importación. He actualizado el importador para que funcione perfectamente con tu formato.

## 🔧 Campos detectados en tu Excel:

| Campo Excel | Se mapea a | Descripción |
|-------------|-----------|-------------|
| `numero_ced` | CI del votante | Cédula de identidad |
| `CELULAR` | Teléfono | Número de celular |
| `apellido` | Apellidos | Apellidos del votante |
| `nombre` | Nombres | Nombres del votante |
| `SEXO` | Género | M/F (se convierte automáticamente) |
| `direccion` | Dirección | Dirección completa |
| `fecha_naci` | Fecha de nacimiento | Se parsea automáticamente |
| `desc_dis` | Distrito | CAACUPE (información administrativa) |

### 📊 Información adicional que se guardará en notas:
- Partido político
- Historial electoral (gral2021, anr2022, gral2023)
- Si es funcionario público
- Si es jubilado
- Profesión (si es abogado)

## 🚀 Cómo importar tus dados:

### 1. Acceder al importador
```
http://tu-dominio/importar
```

### 2. Pasos para la importación:

1. **Seleccionar archivo**: Elige el archivo "CAACUPE ANR 2026.xlsx"
2. **Seleccionar líder**: Asigna un líder territorial (opcional)
3. **Configurar opciones**:
   - ✅ **Actualizar duplicados**: Si quieres actualizar votantes existentes
   - ❌ **Consultar TSJE**: Desactivar (tu archivo ya tiene datos completos)
   - ❌ **Solo CI**: Desactivar (importar datos completos)

4. **Hacer clic en "Importar"**

### 3. El sistema automáticamente:
- ✅ Detectará que es un archivo personalizado (no TSJE estándar)
- ✅ Encontrará los encabezados en la fila 2
- ✅ Importará todos los votantes desde la fila 3 en adelante
- ✅ Mapeará todos los campos correctamente
- ✅ Guardará información adicional en las notas
- ✅ Detectará y evitará duplicados por CI

## 📋 Resultado esperado:

Después de la importación verás un resumen como:
```
✅ Importación completada
- X votantes procesados exitosamente
- Y votantes actualizados
- Z duplicados encontrados
```

## 🔍 Verificación:

1. Ve a la lista de votantes: `http://tu-dominio/votantes`
2. Busca por CI o nombre para verificar que los datos se importaron correctamente
3. Revisa las "notas" de algunos votantes para confirmar que se guardó la información adicional

## ⚡ Consejos:

- **El archivo es grande**: La importación puede tomar varios minutos
- **No cierres la ventana**: Durante la importación, mantén la página abierta
- **Verifica duplicados**: Si tienes votantes existentes, decide si quieres actualizarlos
- **Backup**: Siempre haz una copia de seguridad de tu base de datos antes de importaciones grandes

## 🆘 ¿Problemas?

Si encuentras errores durante la importación:
1. Revisa el log de errores que aparece al final
2. Los errores más comunes son por CIs duplicados o formatos de fecha
3. Puedes importar por partes si el archivo es muy grande

¡El sistema está optimizado para tu formato específico y debería funcionar perfectamente! 🎉