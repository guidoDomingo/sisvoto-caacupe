# Sistema de Roles - SisVoto

## Nuevos Roles Implementados

### 1. **Admin** 
- **Acceso total** al sistema
- Puede gestionar usuarios, votantes, viajes y visitas
- **Puede marcar votos** de los votantes
- Puede eliminar registros
- Acceso a todas las funcionalidades del sistema

### 2. **Líder**
- Acceso a **sus propios votantes** únicamente
- Puede crear, editar y ver votantes asignados a él
- Puede gestionar **viajes y visitas**
- **NO puede marcar votos** - esta restricción es clave
- Puede registrar contactos con votantes
- Ve reportes de su área de responsabilidad

### 3. **Veedor**
- Acceso a **todos los votantes** del sistema (solo lectura)
- **Puede marcar votos** - función principal del veedor
- NO puede crear o editar votantes
- NO puede gestionar viajes o visitas
- Acceso limitado a reportes

## Funcionalidades por Rol

### Gestión de Votantes

| Acción | Admin | Líder | Veedor |
|--------|-------|--------|---------|
| Ver votantes | ✅ Todos | ✅ Propios | ✅ Todos |
| Crear votantes | ✅ | ✅ | ❌ |
| Editar votantes | ✅ | ✅ | ❌ |
| Eliminar votantes | ✅ | ❌ | ❌ |
| **Marcar votos** | ✅ | ❌ | ✅ |

### Gestión de Viajes

| Acción | Admin | Líder | Veedor |
|--------|-------|--------|---------|
| Ver viajes | ✅ Todos | ✅ Propios | ✅ Todos |
| Crear viajes | ✅ | ✅ | ❌ |
| Editar viajes | ✅ | ✅ | ❌ |
| Eliminar viajes | ✅ | ❌ | ❌ |

### Gestión de Visitas

| Acción | Admin | Líder | Veedor |
|--------|-------|--------|---------|
| Ver visitas | ✅ Todas | ✅ Propias | ✅ Todas |
| Crear visitas | ✅ | ✅ | ❌ |
| Editar visitas | ✅ | ✅ | ❌ |
| Eliminar visitas | ✅ | ❌ | ❌ |

## Permisos Técnicos

### Admin
```php
[
    'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar',
    'votantes.todos', 'votantes.crear', 'votantes.editar', 
    'votantes.eliminar', 'votantes.marcar_voto',
    'viajes.todos', 'viajes.crear', 'viajes.editar', 'viajes.eliminar',
    'visitas.todas', 'visitas.crear', 'visitas.editar', 'visitas.eliminar',
    'gastos.aprobar', 'reportes.avanzados', 'configuracion.sistema',
    'auditorias.ver'
]
```

### Líder
```php
[
    'votantes.ver', 'votantes.crear', 'votantes.editar', 'votantes.propios',
    'contactos.registrar',
    'viajes.ver', 'viajes.crear', 'viajes.editar', 'viajes.solicitar',
    'visitas.ver', 'visitas.crear', 'visitas.editar',
    'reportes.propios'
]
```

### Veedor
```php
[
    'votantes.ver', 'votantes.marcar_voto',
    'contactos.ver', 'reportes.ver'
]
```

## Cambios en las Vistas

### Lista de Votantes
- **Botón "Marcar voto"**: Solo visible para Admin y Veedor
- **Botón "Editar"**: Solo visible para Admin y Líder (con sus propios votantes)
- **Botón "Eliminar"**: Solo visible para Admin

### Navegación
- Las opciones del menú se filtran automáticamente según el rol
- Los líderes solo ven sus datos
- Los veedores tienen acceso limitado pero pueden marcar votos

## Comandos Útiles

### Asignar roles a usuarios
```bash
php artisan roles:assign
```

### Verificar roles
```bash
php artisan tinker
>>> App\Models\Role::all(['nombre', 'slug'])
>>> App\Models\User::with('role')->get(['name', 'email', 'role_id'])
```

### Verificar permisos de un usuario
```php
$user = Auth::user();
$user->esAdmin();           // true/false
$user->esLider();           // true/false
$user->esVeedor();          // true/false
$user->puedeMarcarVotos();  // true/false
```

## Notas Importantes

1. **Compatibilidad**: Los métodos antiguos como `esSuperAdmin()` y `esCoordinador()` siguen funcionando para mantener compatibilidad.

2. **Seguridad**: Los permisos se verifican tanto en el backend (Livewire) como en el frontend (Blade).

3. **Migración**: La migración actualiza automáticamente los roles existentes. Los usuarios con líderes asignados se convierten en "Líder", el resto se puede asignar manualmente.

4. **Flexibilidad**: El sistema de permisos es granular, permitiendo ajustes futuros fácilmente.

## Casos de Uso

### Día de Elecciones
- **Veedores** van a los centros de votación y marcan en tiempo real quién ya votó
- **Líderes** coordinan el transporte y las visitas, pero no marcan votos
- **Admin** supervisa todo el proceso y puede intervenir cuando sea necesario

### Campaña Electoral
- **Líderes** gestionan sus territorios, registran votantes, planifican viajes
- **Veedores** apoyan en el registro de votos durante eventos o verificaciones
- **Admin** coordina la estrategia general y gestiona los recursos