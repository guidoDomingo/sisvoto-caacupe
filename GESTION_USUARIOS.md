# Módulo de Gestión de Usuarios

## Descripción
Módulo completo para que los administradores puedan crear, editar y gestionar usuarios del sistema, así como asignar roles y permisos.

## Acceso
- **Solo disponible para usuarios con rol Admin**
- Ruta: `/usuarios` 
- Enlace en el menú lateral izquierdo: "Gestión de Usuarios"

## Funcionalidades

### ✅ Lista de Usuarios
- Vista de todos los usuarios del sistema
- Información mostrada:
  - Avatar (iniciales del nombre)
  - Nombre completo
  - Email
  - CI (si está disponible)
  - Rol asignado (con colores distintivos)
  - Estado (Activo/Inactivo)
  - Último acceso

### ✅ Filtros y Búsqueda
- **Búsqueda**: Por nombre, email o CI
- **Filtro por Rol**: Admin, Líder, Veedor
- **Filtro por Estado**: Activo, Inactivo
- **Limpiar Filtros**: Botón para resetear todos los filtros

### ✅ Crear Usuario
- Formulario completo con validaciones:
  - Nombre completo (obligatorio)
  - Email (obligatorio, único)
  - CI (opcional, único)
  - Teléfono (opcional)
  - Rol (obligatorio)
  - Estado activo/inactivo
  - Contraseña (obligatorio para nuevos usuarios)
  - Confirmación de contraseña

### ✅ Editar Usuario
- Edición de todos los campos
- Contraseña opcional (mantiene la actual si se deja en blanco)
- No permite editar el propio usuario desde las acciones

### ✅ Acciones por Usuario

#### Editar
- Icono de lápiz azul
- Abre modal con datos precargados

#### Activar/Desactivar
- Icono naranja para desactivar usuarios activos
- Icono verde para activar usuarios inactivos
- Confirmación antes de cambiar estado
- Protección: No permite desactivar la propia cuenta

#### Eliminar
- Icono rojo de papelera
- Confirmación antes de eliminar
- Verificaciones de seguridad:
  - No permite eliminar la propia cuenta
  - No permite eliminar usuarios con datos asociados (votantes, contactos)
- Eliminación permanente de la base de datos

### ✅ Validaciones y Seguridad

#### Acceso
- Middleware `AdminMiddleware` protege todas las rutas
- Verificación adicional en el componente Livewire
- Error 403 si un usuario no admin intenta acceder

#### Formulario
- Email único en todo el sistema
- CI único (si se proporciona)
- Contraseñas con política de seguridad de Laravel
- Validaciones en tiempo real con Livewire

#### Protecciones
- No modificar/eliminar cuenta propia
- Verificar datos relacionados antes de eliminar
- Manejo de errores con try-catch

## Características Técnicas

### Componente Livewire
- **Clase**: `App\Livewire\UserManagement`
- **Vista**: `resources/views/livewire/user-management.blade.php`
- Paginación automática
- Búsqueda en tiempo real
- Modal responsive

### Rutas Protegidas
```php
Route::middleware(['admin'])->group(function () {
    Route::get('/usuarios', UserManagement::class)->name('usuarios.index');
});
```

### Middleware
- **AdminMiddleware**: Verifica que el usuario sea admin
- Registrado en `app/Http/Kernel.php`

### Actualización de Último Acceso
- Listener en `EventServiceProvider` para actualizar `ultimo_acceso` en cada login
- Se muestra en la tabla como "hace X tiempo" o "Nunca"

## Interfaz de Usuario

### Colores por Rol
- **Admin**: Púrpura (`bg-purple-100 text-purple-800`)
- **Líder**: Azul (`bg-blue-100 text-blue-800`)
- **Veedor**: Verde (`bg-green-100 text-green-800`)
- **Sin rol**: Rojo (`bg-red-100 text-red-800`)

### Responsive Design
- Modal adaptable a diferentes tamaños de pantalla
- Tabla con scroll horizontal en móviles
- Grid responsive en formularios

### Iconografía
- SVG icons para todas las acciones
- Tooltips informativos
- Estados visuales claros

## Casos de Uso

### Administración Inicial
1. El admin puede crear usuarios para líderes y veedores
2. Asignar roles específicos según responsabilidades
3. Activar/desactivar usuarios según necesidades de campaña

### Gestión Operativa
1. Búsqueda rápida de usuarios por cualquier campo
2. Cambio de roles cuando cambian responsabilidades
3. Desactivación temporal sin perder datos

### Mantenimiento
1. Limpieza de usuarios inactivos o duplicados
2. Verificación de últimos accesos
3. Gestión de permisos centralizada

## Navegación

### En el Menú
- Solo visible para usuarios Admin
- Icono distintivo de gestión de usuarios
- Resaltado cuando está activo

### Breadcrumbs
- Integrado con el layout de la aplicación
- Título y descripción claros

## Próximas Mejoras Sugeridas

1. **Roles y Permisos Granulares**
   - Gestión individual de permisos
   - Roles personalizados

2. **Importación Masiva**
   - Upload de CSV/Excel con usuarios
   - Validaciones en lote

3. **Auditoría**
   - Log de cambios en usuarios
   - Historia de actividades

4. **Notificaciones**
   - Email de bienvenida a nuevos usuarios
   - Notificación de cambios de rol

5. **Perfil de Usuario**
   - Edición de perfil propio
   - Cambio de contraseña individual