# 📊 Análisis Completo del Proyecto TREVSA

**Fecha de Análisis:** 2025-01-27  
**Versión del Framework:** Laravel 12.0  
**Base de Datos:** MongoDB (mongodb/laravel-mongodb v5.5)  
**Estado del Proyecto:** Funcional en Producción

---

## 🎯 Resumen Ejecutivo

**TREVSA** es una aplicación web completa de gestión de servicios de transporte desarrollada con Laravel 12 y MongoDB. El sistema permite gestionar de manera integral el ciclo de vida de servicios de transporte, desde la captación de clientes y proveedores hasta la facturación y seguimiento de estados.

---

## 📋 Resumen de Funcionalidades Principales

### 🔐 Sistema de Roles y Permisos

El sistema cuenta con **4 tipos de usuarios** con diferentes niveles de acceso:

1. **Administrador (Admin)**
   - Acceso completo a todas las funciones del sistema
   - Puede gestionar todo: clientes, proveedores, servicios, transportistas, tarifas y unidades

2. **Operador**
   - Puede gestionar servicios, clientes, proveedores y unidades
   - No puede administrar transportistas
   - Acceso completo a operaciones del día a día

3. **Visor**
   - Solo puede ver información
   - No puede crear, editar ni eliminar registros
   - Ideal para consultas y reportes

4. **Transportista**
   - Acceso limitado a su propia información
   - Solo puede ver y gestionar sus propias unidades disponibles
   - No puede ver información de otros transportistas
   - No tiene acceso a módulos de administración

### 📝 Funciones de Altas de Proveedores

El módulo de **Altas de Proveedores** permite gestionar el proceso completo de registro de nuevos transportistas:

**¿Qué puede hacer el administrador?**
- Registrar todos los datos del proveedor (empresa, contacto, unidades disponibles)
- Cargar documentos requeridos (contratos, identificaciones, seguros, licencias, etc.)
- Ver el estado de la documentación (qué documentos faltan o están completos)
- Dar de alta al proveedor cuando toda la documentación esté completa

**¿Qué sucede al dar de alta?**
- El sistema crea automáticamente una cuenta de usuario para el transportista
- Se genera un email y una contraseña temporal
- Las credenciales se muestran en un mensaje para que el administrador las guarde
- El transportista puede usar estas credenciales para acceder al sistema

**¿Qué puede hacer el transportista después?**
- Iniciar sesión con las credenciales proporcionadas
- Ver y gestionar solo sus propias unidades disponibles
- Registrar nuevas unidades cuando estén disponibles
- Editar información de sus unidades existentes
- No puede ver ni modificar información de otros transportistas

---

### Características Principales Implementadas:
- ✅ Sistema de autenticación con roles y permisos granulares
- ✅ **Sistema RBAC completo con aislamiento de datos por usuario (Row-Level Security)**
- ✅ **Creación automática de usuarios para transportistas al dar de alta proveedores**
- ✅ Dashboard interactivo con KPIs, gráficos y filtros de fecha
- ✅ Gestión completa de servicios de transporte con máquina de estados
- ✅ Sistema de cálculo automático de tarifas con integración Google Maps
- ✅ Gestión de clientes, proveedores y transportistas
- ✅ Sistema de altas de proveedores con carga de documentos
- ✅ Exportación de datos a Excel (XLSX)
- ✅ Interfaz moderna y responsiva con Tailwind CSS y Alpine.js
- ✅ Sistema de historial para cambios de estado y tarifas
- ✅ Manejo estructurado de errores con logging JSON

---

## 🏗️ Arquitectura del Proyecto

### Stack Tecnológico

#### Backend
- **Framework:** Laravel 12.0
- **PHP:** ^8.2
- **Base de Datos:** MongoDB (mongodb/laravel-mongodb ^5.5)
- **Autenticación:** Laravel Breeze ^2.3
- **Exportación:** Maatwebsite Excel ^3.1
- **Lenguaje:** PHP 8.2+

#### Frontend
- **CSS Framework:** Tailwind CSS ^3.1.0
- **JavaScript:** Alpine.js ^3.4.2
- **Build Tool:** Vite ^7.0.7
- **Gráficos:** Chart.js ^4.5.1
- **HTTP Client:** Axios ^1.11.0
- **Formularios:** @tailwindcss/forms ^0.5.2

#### Desarrollo
- **Testing:** PHPUnit ^11.5.3
- **Code Style:** Laravel Pint ^1.24
- **Logging:** Laravel Pail ^1.2.2
- **Concurrencia:** concurrently ^9.0.1

---

## 📁 Estructura del Proyecto

### Modelos Principales (MongoDB)

#### 1. **Servicio** (`app/Models/Servicio.php`)
**Colección:** `servicios` (antes: `registro_solicitudes`)

Representa los servicios/solicitudes de transporte solicitados por clientes.

**Campos principales:**
- `cliente_id`, `cliente_nombre`
- `proveedor_id`, `proveedor_nombre`
- `tipo_transporte`, `tipo_carga`, `peso_carga`
- `origen`, `destino`
- `fecha_servicio`, `hora_servicio`
- `tarifa_cliente`, `tarifa_proveedor`
- `distancia_km`, `costo_diesel`, `margen_calculado`
- `estado` (pendiente, confirmado, en_carga, en_transito, entregado, facturado, cancelado)
- `comentarios`, `created_by`

**Relaciones:**
- `belongsTo(Cliente::class)`
- `belongsTo(Proveedor::class)`
- `hasMany(EstadoHistorial::class)`
- `hasMany(TarifaHistorial::class)`

#### 2. **Cliente** (`app/Models/Cliente.php`)
**Colección:** `clientes`

Representa prospectos y clientes potenciales.

**Campos principales:**
- `nombre_empresa`, `nombre_contacto`
- `telefono`, `email`
- `ciudad`, `estado`, `industria`
- `comentarios`, `estado_prospecto`
- `created_by`

**Relaciones:**
- `hasMany(Servicio::class)`

#### 3. **Proveedor** (`app/Models/Proveedor.php`)
**Colección:** `proveedores`

Representa prospectos de proveedores con información de unidades.

**Campos principales:**
- `nombre_empresa`, `telefono`, `email`
- `cantidad_unidades` (int)
- `tipos_unidades`, `cantidades_unidades` (array)
- `base_linea_transporte`, `corredor_linea_transporte`
- `nombre_quien_registro`, `notas`
- `estado_prospecto`, `created_by`

**Métodos especiales:**
- `getFormattedIdAttribute()`: Retorna ID formateado como PROV-xxx
- `tieneAltaCompleta()`: Verifica si tiene alta completa

**Relaciones:**
- `hasMany(Servicio::class)`
- `hasOne(TransporteProveedor::class)`

#### 4. **Transportista** (`app/Models/Transportista.php`)
**Colección:** `transportistas` (antes: `transportistas_inv`)

Transportistas con inventario de unidades.

#### 5. **UnidadDisponible** (`app/Models/UnidadDisponible.php`)
**Colección:** `unidades_disponibles`

Unidades disponibles para servicios.

**Campos principales:**
- `transporte_proveedor_id`, `user_id` (para Row-Level Security)
- `nombre_transportista`
- `unidades_disponibles`, `cantidades_unidades` (arrays)
- `lugar_disponible`, `fecha_disponible`, `hora_disponible`
- `destino_sugerido`, `notas`, `estatus`, `created_by`

**Relaciones:**
- `belongsTo(TransporteProveedor::class)`
- `belongsTo(User::class)` - Para aislamiento de datos por usuario

**Scopes:**
- `scopeForUser($query, $userId)`: Filtra unidades por usuario (Row-Level Security)

#### 6. **Tarifa** (`app/Models/Tarifa.php`)
**Colección:** `tarifas` (antes: `tarifas_trevsa`)

Configuración de tarifas base del sistema.

#### 7. **PrecioDiesel** (`app/Models/PrecioDiesel.php`)
**Colección:** `precio_diesel`

Precios históricos de diésel.

**Métodos estáticos:**
- `precioActual()`: Obtiene el precio actual de diésel

#### 8. **EstadoHistorial** (`app/Models/EstadoHistorial.php`)
**Colección:** `estado_historial`

Historial de cambios de estado de servicios.

**Campos:** `servicio_id`, `estado_anterior`, `estado_nuevo`, `comentario`, `changed_by`, `changed_at`

#### 9. **TarifaHistorial** (`app/Models/TarifaHistorial.php`)
**Colección:** `tarifa_historial`

Historial de cambios de tarifas en servicios.

**Campos:** `servicio_id`, `cambios` (array), `distancia_km`, `costo_diesel`, `margen_calculado`, `changed_by`, `changed_at`

#### 10. **TransporteProveedor** (`app/Models/TransporteProveedor.php`)
**Colección:** `transportes_proveedores`

Altas completas de proveedores con documentos.

**Campos principales:**
- `proveedor_id`, `user_id` (ID del usuario/transportista asociado)
- `nombre_solicita`
- `unidades`, `cantidades_unidades`, `unidades_otros` (arrays)
- Documentos: `contrato_files`, `formato_alta_file`, `ine_dueno_files`, etc.
- `status`, `created_by`

**Relaciones:**
- `belongsTo(Proveedor::class)`
- `belongsTo(User::class)` - Usuario/transportista asociado
- `hasMany(UnidadDisponible::class)`

**Métodos especiales:**
- `validarDocumentosRequeridos()`: Retorna array de documentos faltantes
- `tieneTodosLosDocumentos()`: Verifica si tiene todos los documentos
- `getFormattedIdAttribute()`: Retorna ID formateado como ALT-xxx

#### 11. **User** (`app/Models/User.php`)
**Colección:** `users`

Usuarios del sistema (extiende `MongoDB\Laravel\Auth\User`).

**Campos:** `name`, `email`, `password`, `role` (legacy), `roles[]`, `permissions[]`

**Métodos principales:**
- `assignedRoles()`: Obtiene todos los roles asignados
- `hasRole(...$roles)`: Verifica si tiene alguno de los roles
- `hasPermission($permission)`: Verifica si tiene un permiso
- `resolvePermissions()`: Calcula permisos desde roles y permisos personalizados
- `syncRoles($roles)`: Sincroniza roles
- `syncPermissions($permissions)`: Sincroniza permisos personalizados

**Relaciones:**
- `hasOne(TransporteProveedor::class)` - Alta de proveedor asociada
- `hasMany(UnidadDisponible::class)` - Unidades disponibles del transportista

---

## 🎮 Controladores Principales

### 1. **DashboardController**
**Ruta:** `/dashboard`  
**Permiso:** `dashboard.view`

**Funcionalidad:**
- KPIs: Total servicios, en tránsito, finalizadas, alertas
- Métricas financieras: Ingresos, costos, margen total
- Gráficos de tendencias (día/semana/mes según rango de fechas)
- Distribución por estado y tipo de transporte
- Comparativas con períodos anteriores
- Últimas 10 cargas recientes
- **Cache de 5 minutos** para optimización de rendimiento

**Métodos clave:**
- `index()`: Vista principal con métricas
- `calculateDashboardMetrics()`: Cálculo de todas las métricas
- `buildTendencias()`: Construcción de datos para gráficos
- `determinePeriodo()`: Determina agrupación según rango de fechas

### 2. **RegistroSolicitudesController**
**Rutas:** `/registro`  
**Permisos:** `registro.view`, `registro.manage`

**Funcionalidad:**
- CRUD completo de servicios
- Filtros avanzados: fecha, estado, cliente, proveedor
- Paginación y ordenamiento
- Integración con cálculo de tarifas
- Búsqueda instantánea

### 3. **ClientesController**
**Rutas:** `/clientes`  
**Permisos:** `clientes.view`, `clientes.manage`

**Funcionalidad:**
- CRUD de clientes/prospectos
- Filtros y búsqueda
- Validación mediante Form Request (`StoreClienteRequest`, `UpdateClienteRequest`)

### 4. **ProveedoresController**
**Rutas:** `/prospectos-proveedores`, `/proveedores`  
**Permisos:** `proveedores.view`, `proveedores.manage`

**Funcionalidad:**
- CRUD de proveedores/prospectos
- Exportación a Excel de tipos de unidades (`exportTiposUnidades()`)
- Gestión de tipos y cantidades de unidades
- ID formateado PROV-xxx

### 5. **UnidadesController**
**Rutas:** `/unidades`  
**Permisos:** `unidades.view`, `unidades.manage`

**Funcionalidad:**
- CRUD de unidades disponibles
- **Row-Level Security:** Filtrado automático por `user_id` para transportistas
- **Validaciones de seguridad:** Los transportistas solo pueden ver/editar sus propias unidades
- API para búsqueda de proveedores (`buscarProveedores()`) con filtrado por usuario
- Gestión de disponibilidad

**Métodos con Row-Level Security:**
- `index()`: Filtra unidades por `user_id` si el usuario es transportista
- `create()`: Solo muestra proveedores del usuario si es transportista
- `store()`: Valida que el proveedor pertenezca al usuario y asigna `user_id`
- `edit()`: Valida que la unidad pertenezca al usuario antes de permitir edición
- `update()`: Valida propiedad y proveedor antes de actualizar

### 6. **AltasProveedoresController**
**Rutas:** `/altas-proveedores`  
**Permisos:** `altas.view`, `altas.manage`

**Funcionalidad:**
- Gestión completa de altas de proveedores
- Carga de documentos (hasta 200MB mediante `EnforceUploadLimits`)
- Validación de archivos (MIME types mediante `ValidFileMime`)
- Exportación completa y específica a Excel
- Método `darAlta()`: Proceso de alta completa
- **Creación automática de usuarios:** Genera usuario con rol `transportista` al dar de alta
- **Visualización de credenciales:** Modal con email y contraseña temporal generada
- Método `crearUsuarioTransportista()`: Crea usuario automáticamente con email y contraseña temporal

### 7. **TarifasController**
**Rutas:** `/tarifas`  
**Permisos:** `tarifas.view`, `tarifas.manage`, `tarifas.precio`

**Funcionalidad:**
- Vista de cálculo de tarifas
- Gestión de precio de diésel (`precioDiesel()`, `actualizarPrecioDiesel()`)
- Historial de tarifas (`historial()`)
- Guardado de cálculos (`guardarCalculo()`)

### 8. **CalculoTarifaController**
**Rutas:** `/api/calcular-distancia`, `/api/calcular-tarifa`  
**Permisos:** `tarifas.manage`

**Funcionalidad:**
- API para cálculo de distancia (integración con Google Maps)
- API para cálculo de tarifa completa
- Integración con `TarifaService` y `DistanciaService`

### 9. **EstadoController**
**Rutas:** `/servicio/{id}/estado`  
**Permisos:** `registro.manage`

**Funcionalidad:**
- Cambio de estado de servicios
- Validación de transiciones mediante `EstadoService`
- Historial de cambios
- Vista para cambio de estado con estados permitidos

### 10. **TransportistaController**
**Rutas:** `/transportistas`  
**Permisos:** `transportistas.view`, `transportistas.manage`

**Funcionalidad:**
- CRUD de transportistas
- Gestión de inventario de unidades

---

## 🔧 Servicios (Business Logic)

### 1. **TarifaService** (`app/Services/TarifaService.php`)

**Métodos estáticos:**
- `calcularCostoDiesel($distanciaKm, $consumoPorKm = 0.35)`: Calcula costo de diésel basado en distancia y precio actual
- `calcularMargen($tarifaCliente, $costoDiesel, $tarifaProveedor = null)`: Calcula margen de ganancia
- `calcularMargenPorcentual($margen, $tarifaCliente)`: Calcula margen porcentual
- `guardarHistorial(Servicio, array, array, ?int)`: Guarda historial de cambios de tarifa

### 2. **EstadoService** (`app/Services/EstadoService.php`)

**Estados válidos:**
- `pendiente`, `confirmado`, `en_carga`, `en_transito`, `entregado`, `facturado`, `cancelado`

**Transiciones permitidas:**
- `pendiente` → `confirmado`, `cancelado`
- `confirmado` → `en_carga`, `cancelado`
- `en_carga` → `en_transito`, `cancelado`
- `en_transito` → `entregado`, `cancelado`
- `entregado` → `facturado`
- `facturado` → (estado final)
- `cancelado` → (estado final)

**Métodos estáticos:**
- `esTransicionValida($estadoActual, $estadoNuevo)`: Valida transiciones
- `obtenerEstadosPermitidos($estadoActual)`: Obtiene estados permitidos
- `cambiarEstado(Servicio, string, ?string, ?int)`: Cambia estado y guarda historial
- `obtenerHistorial(string)`: Obtiene historial de cambios
- `obtenerEtiqueta(string)`: Obtiene etiqueta legible
- `obtenerColor(string)`: Obtiene clase CSS para color

### 3. **DistanciaService** (`app/Services/DistanciaService.php`)

**Funcionalidad:**
- Calcula distancias entre origen y destino
- Integración con Google Maps API
- Retorna distancia en kilómetros

### 4. **AltaProveedorService** (`app/Services/AltaProveedorService.php`)

**Funcionalidad:**
- Lógica de negocio para altas de proveedores
- Validación y procesamiento de documentos
- Gestión de archivos subidos

---

## 🔐 Sistema de Autenticación y Permisos

### Configuración
**Archivo:** `config/permissions.php`

### Roles Definidos:

#### 1. **admin**
Acceso completo a todos los módulos.

**Permisos:**
- `dashboard.view`
- `transportistas.view`, `transportistas.manage`
- `registro.view`, `registro.manage`
- `clientes.view`, `clientes.manage`
- `proveedores.view`, `proveedores.manage`
- `altas.view`, `altas.manage`
- `tarifas.view`, `tarifas.manage`, `tarifas.precio`
- `unidades.view`, `unidades.manage`

#### 2. **operador**
Gestión operativa (sin administración de transportistas).

**Permisos:**
- `dashboard.view`
- `registro.view`, `registro.manage`
- `clientes.view`, `clientes.manage`
- `proveedores.view`, `proveedores.manage`
- `altas.view`, `altas.manage`
- `tarifas.view`, `tarifas.manage`
- `unidades.view`, `unidades.manage`

#### 3. **visor**
Solo lectura en todos los módulos.

**Permisos:**
- `dashboard.view`
- `registro.view`
- `clientes.view`
- `proveedores.view`
- `altas.view`
- `tarifas.view`
- `unidades.view`

#### 4. **transportista**
Acceso limitado enfocado en sus propios datos.

**Permisos:**
- `dashboard.view`
- `unidades.view`
- `unidades.manage`

**Restricciones:**
- ❌ NO tiene acceso a módulos de administración (P. Proveedores, Altas Proveedores, P. Clientes, Servicios, Transportistas, Tarifas)
- ✅ Solo puede ver, editar y crear sus propias unidades disponibles (Row-Level Security)
- ✅ Aislamiento de datos: No puede ver datos de otros transportistas

**Rol por defecto:** `visor`

### Middleware
**Archivo:** `app/Http/Middleware/EnsurePermission.php`

**Funcionalidad:**
- Valida permisos antes de acceder a rutas
- Uso: `middleware('permission:permiso.nombre')`
- Soporta múltiples permisos (OR lógico)
- Retorna 403 si no tiene permisos

**Registro:** `app/Http/Kernel.php` con alias `permission`

### Modelo User
- Extiende `MongoDB\Laravel\Auth\User`
- Implementa sistema de roles y permisos
- Compatible con campo legacy `role` y nuevo sistema `roles[]`
- Permisos personalizados por usuario mediante `permissions[]`

---

## 🎨 Frontend y UI

### Estilos

#### Framework: Tailwind CSS 3.1.0

**Tema personalizado Trevsa:**
- Colores personalizados: `trevsa-red`, `trevsa-black`, `trevsa-white`
- Fuentes: Poppins (sans), Space Grotesk (display)
- Animaciones personalizadas: `slide-in-right`, `fade-in`, `scale-in`, `slide-up`
- Diseño responsivo con breakpoints estándar

**Archivos:**
- `resources/css/app.css`: Estilos base
- `resources/css/custom-theme.css`: Tema personalizado Trevsa
- `tailwind.config.js`: Configuración de Tailwind

### JavaScript

#### Alpine.js 3.4.2
Interactividad reactiva en componentes.

#### Chart.js 4.5.1
Gráficos en dashboard (tendencias, distribuciones).

#### Axios 1.11.0
Peticiones HTTP asíncronas.

### Componentes Blade Reutilizables

**Ubicación:** `resources/views/components/`

**22 componentes disponibles:**
1. `alert.blade.php` - Alertas
2. `application-logo.blade.php` - Logo de la aplicación
3. `auth-session-status.blade.php` - Estado de sesión
4. `badge.blade.php` - Badges de estado
5. `button.blade.php` - Botones
6. `card.blade.php` - Tarjetas
7. `danger-button.blade.php` - Botón de peligro
8. `dropdown-link.blade.php` - Enlace dropdown
9. `dropdown.blade.php` - Dropdown
10. `form-input.blade.php` - Input de formulario
11. `form-select.blade.php` - Select de formulario
12. `form-textarea.blade.php` - Textarea
13. `input-error.blade.php` - Error de input
14. `input-label.blade.php` - Label de input
15. `input.blade.php` - Input base
16. `modal.blade.php` - Modal
17. `nav-link.blade.php` - Enlace de navegación
18. `primary-button.blade.php` - Botón primario
19. `responsive-nav-link.blade.php` - Enlace responsive
20. `secondary-button.blade.php` - Botón secundario
21. `table.blade.php` - Tabla
22. `text-input.blade.php` - Input de texto

### Vistas Principales

**Layouts:**
- `resources/views/layouts/app.blade.php` - Layout principal (con topbar moderno y fondo personalizado)
- `resources/views/layouts/guest.blade.php` - Layout invitado
- `resources/views/layouts/navigation.blade.php` - Navegación

**Módulos:**
- `dashboard.blade.php` - Dashboard principal
- `altas_proveedores/` (5 vistas)
- `clientes/` (3 vistas)
- `proveedores/` (3 vistas)
- `registro/` (3 vistas)
- `tarifas/` (4 vistas)
- `transportistas/` (3 vistas)
- `unidades/` (3 vistas)
- `estado/` (1 vista)

**Vistas de error:**
- `errors/403.blade.php` - Acceso denegado
- `errors/404.blade.php` - No encontrado
- `errors/500.blade.php` - Error del servidor

---

## 📊 Base de Datos (MongoDB)

### Configuración
**Archivo:** `config/database.php`

**Conexión:**
```php
'mongodb' => [
    'driver' => 'mongodb',
    'dsn' => env('MONGO_DSN', ''),
    'host' => env('DB_HOST', '127.0.0.1'),
    'port' => env('DB_PORT', 27017),
    'database' => env('DB_DATABASE', 'trevsa_db'),
    'username' => env('DB_USERNAME', ''),
    'password' => env('DB_PASSWORD', ''),
    'options' => [
        'database' => env('MONGO_AUTH_DB', 'admin'),
    ],
]
```

**Conexión por defecto:** SQLite (para autenticación de Laravel)
**Conexión principal:** MongoDB (para modelos de negocio)

### Colecciones Principales

1. `servicios` (antes: `registro_solicitudes`)
2. `clientes`
3. `proveedores`
4. `transportistas` (antes: `transportistas_inv`)
5. `unidades_disponibles`
6. `tarifas` (antes: `tarifas_trevsa`)
7. `precio_diesel`
8. `estado_historial`
9. `tarifa_historial`
10. `transportes_proveedores`
11. `users`

### Migración de Datos

**Documento:** `MIGRACION_DATOS.md`

**Comando:** `php artisan models:migrate`

**Opciones:**
- `--dry-run`: Modo de prueba sin modificar datos
- `--backup`: Crea backups antes de migrar

**Script:** `app/Console/Commands/MigrateModelsData.php`

**Funcionalidad:**
- Migra colecciones antiguas a nuevas estructuras
- Mapea campos antiguos a nuevos
- Crea backups automáticos
- Mantiene integridad de datos

---

## 🛡️ Seguridad y Validación

### Form Requests

**Ubicación:** `app/Http/Requests/`

**9 Form Requests implementados:**

1. **StoreAltaProveedorRequest**
   - Validación de archivos (MIME types)
   - Límite de tamaño (200MB)
   - Regla personalizada: `ValidFileMime`

2. **StoreClienteRequest**
   - Validación de datos de cliente
   - Email único, teléfono válido

3. **UpdateClienteRequest**
   - Validación de actualización de cliente

4. **StoreProveedorRequest**
   - Validación de datos de proveedor
   - Arrays para tipos y cantidades de unidades

5. **UpdateProveedorRequest**
   - Validación de actualización de proveedor

6. **StoreServicioRequest**
   - Validación de datos de servicio
   - Fechas válidas, tarifas numéricas

7. **UpdateServicioRequest**
   - Validación de actualización de servicio

8. **StoreUnidadRequest**
   - Validación de unidades disponibles

9. **UpdateUnidadRequest**
   - Validación de actualización de unidades

### Middleware de Seguridad

#### 1. **EnforceUploadLimits** (`app/Http/Middleware/EnforceUploadLimits.php`)
**Funcionalidad:**
- Límite de 200MB para uploads
- Configuración dinámica de PHP limits
- Validación de CONTENT_LENGTH antes de procesar

#### 2. **EnsurePermission** (`app/Http/Middleware/EnsurePermission.php`)
**Funcionalidad:**
- Control de acceso basado en permisos
- Retorna 403 si no tiene permisos
- Soporta múltiples permisos (OR lógico)

### Validación de Archivos

**Regla personalizada:** `app/Rules/ValidFileMime.php`

**Tipos MIME permitidos:**
- PDF: `application/pdf`
- Imágenes: `image/jpeg`, `image/png`, `image/jpg`
- Excel: `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet`, `application/vnd.ms-excel`
- Word: `application/vnd.openxmlformats-officedocument.wordprocessingml.document`, `application/msword`

**Sanitización:**
- Limpieza de nombres de archivo
- Validación de extensiones

### Manejo de Errores

**Archivo:** `app/Exceptions/Handler.php`

**Características:**
- Logging estructurado en JSON (`trevsa-structured.log`)
- Contexto detallado de excepciones
- Sanitización de datos sensibles
- Request ID único para tracking
- Páginas de error personalizadas:
  - `403`: Acceso denegado
  - `404`: No encontrado
  - `500`: Error del servidor

**Logging:**
- Canal `structured`: JSON formateado
- Canal `single`: Logs lineales
- Contexto completo: URL, método, IP, usuario, payload sanitizado

---

## 📤 Exportación de Datos

### Exportadores

**Ubicación:** `app/Exports/`

#### 1. **AltasProveedoresExport** (`AltasProveedoresExport.php`)
**Interfaces:** `FromCollection`, `WithHeadings`, `WithMapping`, `WithStyles`, `WithDrawings`, `WithCustomStartCell`, `WithEvents`

**Métodos:**
- `exportAll()`: Exporta todas las altas
- `exportSpecific($prospectoId)`: Exporta alta específica

**Formato:** XLSX con formato avanzado

#### 2. **TiposUnidadesExport** (`TiposUnidadesExport.php`)
**Interfaces:** `FromCollection`, `WithHeadings`

**Funcionalidad:**
- Exporta tipos de unidades de proveedores
- Formato XLSX estructurado

### Librería
- **Maatwebsite Excel** ^3.1
- Formato: XLSX
- Estilos y formateo avanzado

---

## 🧪 Testing

### Configuración
- **Framework:** PHPUnit ^11.5.3
- **Archivo:** `phpunit.xml`
- **Tests ubicación:** `tests/`

### Tests Disponibles

**Feature Tests:**
- `tests/Feature/Auth/` (6 tests de autenticación)
- `tests/Feature/ExampleTest.php`
- `tests/Feature/ProfileTest.php`

**Unit Tests:**
- `tests/Unit/ExampleTest.php`

### Comando
```bash
composer test
# O
php artisan test
```

---

## 📦 Dependencias Principales

### Backend (Composer)

**Producción:**
- `laravel/framework: ^12.0`
- `mongodb/laravel-mongodb: ^5.5`
- `maatwebsite/excel: ^3.1`
- `laravel/tinker: ^2.10.1`

**Desarrollo:**
- `laravel/breeze: ^2.3`
- `phpunit/phpunit: ^11.5.3`
- `laravel/pint: ^1.24`
- `laravel/pail: ^1.2.2`
- `laravel/sail: ^1.41`
- `fakerphp/faker: ^1.23`

### Frontend (NPM)

**Producción:**
- `chart.js: ^4.5.1`

**Desarrollo:**
- `tailwindcss: ^3.1.0`
- `alpinejs: ^3.4.2`
- `vite: ^7.0.7`
- `axios: ^1.11.0`
- `@tailwindcss/forms: ^0.5.2`
- `@tailwindcss/vite: ^4.0.0`
- `concurrently: ^9.0.1`

---

## 🚀 Scripts y Comandos

### Composer Scripts

```json
{
  "setup": [
    "composer install",
    "@php -r \"file_exists('.env') || copy('.env.example', '.env');\"",
    "@php artisan key:generate",
    "@php artisan migrate --force",
    "npm install",
    "npm run build"
  ],
  "dev": [
    "Composer\\Config::disableProcessTimeout",
    "npx concurrently -c \"#93c5fd,#c4b5fd,#fb7185,#fdba74\" \"php artisan serve\" \"php artisan queue:listen --tries=1\" \"php artisan pail --timeout=0\" \"npm run dev\" --names=server,queue,logs,vite --kill-others"
  ],
  "test": [
    "@php artisan config:clear --ansi",
    "@php artisan test"
  ]
}
```

### NPM Scripts

```json
{
  "dev": "vite",
  "build": "vite build"
}
```

### Artisan Commands

**Comandos personalizados:**
- `php artisan models:migrate`: Migración de datos con opciones `--dry-run`, `--backup`
- `php artisan db:init`: Inicialización de base de datos (si existe)

---

## 📝 Archivos de Configuración Importantes

### 1. `.env` (usar `.env.example` como base)
**Variables importantes:**
- MongoDB: `MONGO_DSN`, `DB_DATABASE`, `DB_HOST`, `DB_PORT`
- Aplicación: `APP_NAME`, `APP_ENV`, `APP_KEY`, `APP_URL`
- Google Maps: `GOOGLE_MAPS_API_KEY` (para cálculo de distancias)
- Email: `MAIL_MAILER`, `MAIL_HOST`, etc.

### 2. `config/permissions.php`
**Contenido:**
- Roles y permisos definidos
- Rol por defecto

### 3. `config/database.php`
**Contenido:**
- Conexiones de base de datos
- Configuración MongoDB

### 4. `tailwind.config.js`
**Contenido:**
- Tema personalizado Trevsa
- Colores, fuentes, animaciones
- Plugins

### 5. `vite.config.js`
**Contenido:**
- Configuración de assets
- Entradas CSS y JS
- Refresh automático

### 6. `config/logging.php`
**Contenido:**
- Canales de logging
- Formato structured (JSON)
- Configuración de rotación

---

## 🔍 Características Destacadas

### 1. Dashboard Interactivo
- ✅ KPIs en tiempo real
- ✅ Gráficos de tendencias (Chart.js)
- ✅ Filtros de fecha personalizables
- ✅ Comparativas con períodos anteriores
- ✅ Cache de 5 minutos para optimización
- ✅ Distribución por estado y tipo
- ✅ Últimas cargas recientes

### 2. Máquina de Estados
- ✅ Validación de transiciones
- ✅ Historial completo de cambios
- ✅ Comentarios en cambios de estado
- ✅ Tracking de usuario que realizó el cambio
- ✅ Estados finales protegidos
- ✅ Colores y etiquetas legibles

### 3. Cálculo Automático de Tarifas
- ✅ Integración con Google Maps API
- ✅ Cálculo de distancia
- ✅ Cálculo de costo de diésel basado en precio actual
- ✅ Cálculo de margen
- ✅ Historial de cambios de tarifa
- ✅ Guardado de cálculos

### 4. Sistema de Permisos Granular
- ✅ Roles predefinidos (admin, operador, visor, transportista)
- ✅ Permisos por módulo (view, manage, precio)
- ✅ Permisos personalizados por usuario
- ✅ Middleware integrado
- ✅ Compatible con sistema legacy
- ✅ **Row-Level Security (RLS):** Aislamiento de datos por usuario para transportistas
- ✅ **Creación automática de usuarios:** Generación de credenciales al dar de alta proveedores

### 5. Exportación de Datos
- ✅ Excel (XLSX)
- ✅ Exportación completa y específica
- ✅ Formato estructurado con estilos
- ✅ Exportación de tipos de unidades

### 6. Validación Robusta
- ✅ Form Requests para cada módulo
- ✅ Validación de archivos (MIME, tamaño)
- ✅ Reglas personalizadas
- ✅ Mensajes de error personalizados
- ✅ Sanitización de datos

### 7. Manejo de Archivos
- ✅ Carga hasta 200MB
- ✅ Validación de MIME types
- ✅ Sanitización de nombres
- ✅ Almacenamiento organizado
- ✅ Middleware de límites

### 8. Logging Estructurado
- ✅ Logs en formato JSON
- ✅ Contexto completo de excepciones
- ✅ Request ID para tracking
- ✅ Sanitización de datos sensibles
- ✅ Múltiples canales

---

## ⚠️ Áreas de Mejora Identificadas

### Críticas (Alta Prioridad)

#### 1. **Sistema de Notificaciones** 🔴
**Estado actual:** No existe
- No hay notificaciones push/email
- No hay recordatorios automáticos
- No hay alertas proactivas

**Recomendación:**
- Implementar `app/Notifications`
- Configurar colas para notificaciones
- Comandos programados para servicios próximos
- Alertas para documentos vencidos

#### 2. **Backup y Recuperación** 🔴
**Estado actual:** Solo backups manuales en migración
- Sin estrategia automatizada
- Sin comandos de backup programados
- Sin política de retención

**Recomendación:**
- Crear comando `BackupDatabase`
- Programar en `Console\Kernel`
- Integrar con servicios externos (S3, etc.)
- Documentar políticas de retención

#### 3. **Integridad Referencial** 🔴
**Estado actual:** No se bloquea eliminación con dependencias
- Puede eliminarse cliente con servicios
- Puede eliminarse proveedor con servicios
- Falta soft deletes

**Recomendación:**
- Verificaciones previas a eliminación
- Soft deletes para entidades principales
- Comandos de saneamiento para datos huérfanos

#### 4. **Importación de Datos** 🟠
**Estado actual:** Solo exportación implementada
- No hay importación masiva
- No hay plantillas Excel
- No hay validación previa

**Recomendación:**
- Definir plantillas Excel estándar
- Implementar importación con Laravel Excel
- Validaciones previas a importación
- Manejo de errores detallado

### Medias (Prioridad Media)

#### 1. **Paginación Unificada** 🟠
**Estado actual:** Filtros implementados en algunos módulos
- Algunos módulos sin filtros avanzados
- Falta exportación directa desde tablas

**Recomendación:**
- Unificar filtros en todos los módulos
- Agregar exportación desde tablas
- Componentes reutilizables para filtros

#### 2. **UI de Administración de Usuarios** 🟡
**Estado actual:** Parcialmente implementado
- ✅ Creación automática de usuarios para transportistas
- ✅ Visualización de credenciales en modal al dar de alta
- ✅ Sección de credenciales en vista de detalles de alta
- ⚠️ Falta panel completo de administración de usuarios
- ⚠️ No hay auditoría de acciones
- ⚠️ Sin seeding de roles

**Recomendación:**
- Construir panel completo de administración de usuarios
- Implementar auditoría de acciones sensibles
- Seeding de roles y permisos
- Funcionalidad para resetear contraseñas desde el panel

#### 3. **Feedback Visual** 🟠
**Estado actual:** Alertas básicas presentes
- Falta toasts, loaders
- Sin confirmaciones modales

**Recomendación:**
- Incorporar librería de toasts (SweetAlert2/Toaster)
- Loaders en acciones críticas
- Confirmaciones modales para acciones destructivas

#### 4. **Validación en Tiempo Real** 🟠
**Estado actual:** Validación solo al enviar
- Formularios sin validación reactiva
- Sin mensajes inline

**Recomendación:**
- Añadir validación en tiempo real con Alpine.js
- Mensajes inline de validación
- Tooltips contextuales

### Bajas (Prioridad Baja)

#### 1. **Autocompletado Inteligente** 🟠
**Estado actual:** Formularios básicos
- Sin búsquedas type-ahead
- Sin sugerencias según ruta

**Recomendación:**
- Endpoints JSON para búsqueda
- Componentes de búsqueda con debounce
- Sugerencias contextuales

#### 2. **Guardado Automático (Draft)** 🔴
**Estado actual:** Sin guardado temporal
- Formularios extensos sin autosave

**Recomendación:**
- Implementar autosave con localStorage
- Guardado de borradores en MongoDB
- Recuperación de borradores

#### 3. **Vista de Detalle Completa** 🟠
**Estado actual:** Vista de estado presente
- Falta vista consolidada de servicios

**Recomendación:**
- Crear `servicios.show` con timeline completo
- Tarifas, comentarios y acciones rápidas
- Vista consolidada con documentos

#### 4. **Accesibilidad** 🔴
**Estado actual:** Sin etiquetas ARIA
- Sin pruebas de contraste documentadas

**Recomendación:**
- Auditar accesibilidad
- Añadir roles ARIA
- Atajos de teclado

#### 5. **Breadcrumbs** 🔴
**Estado actual:** Sin navegación contextual

**Recomendación:**
- Añadir breadcrumbs globales
- Enlaces contextuales por vista

---

## 📚 Documentación Disponible

1. **README.md**: Información básica de Laravel (genérico)
2. **ANALISIS_PROYECTO_COMPLETO.md**: Análisis detallado del proyecto
3. **ANALISIS_COMPLETO_MEJORAS.md**: Análisis de mejoras propuestas
4. **MIGRACION_DATOS.md**: Guía de migración de datos
5. **IMPLEMENTACION_RBAC_TRANSPORTISTA.md**: Documentación completa de la implementación RBAC y Row-Level Security
6. **install.sh**: Script de instalación automatizada
7. **fix_php_limits.sh**: Script para ajustar límites PHP

---

## 🎯 Conclusión

**TREVSA** es una aplicación robusta y bien estructurada para la gestión de servicios de transporte. El proyecto demuestra:

### ✅ Fortalezas:
- ✅ Arquitectura clara y organizada
- ✅ Separación de responsabilidades (Services, Controllers, Models)
- ✅ Sistema de permisos bien implementado con RBAC completo
- ✅ **Row-Level Security (RLS) implementado para aislamiento de datos por usuario**
- ✅ **Creación automática de usuarios con generación de credenciales**
- ✅ Validaciones robustas con Form Requests
- ✅ UI moderna y responsiva con Tailwind CSS
- ✅ Integración sólida con MongoDB
- ✅ Dashboard interactivo con KPIs y gráficos
- ✅ Máquina de estados completa para servicios
- ✅ Cálculo automático de tarifas
- ✅ Historial completo de cambios
- ✅ Exportación de datos a Excel
- ✅ Manejo estructurado de errores con logging JSON
- ✅ Sistema de carga de archivos robusto

### ⚠️ Oportunidades de Mejora:
- ⚠️ Sistema de notificaciones (alta prioridad)
- ⚠️ Backup automatizado (alta prioridad)
- ⚠️ Integridad referencial mejorada (alta prioridad)
- ⚠️ Importación de datos (alta prioridad)
- ⚠️ UI de administración de usuarios (media prioridad)
- ⚠️ Feedback visual mejorado (media prioridad)
- ⚠️ Validación en tiempo real (baja prioridad)
- ⚠️ Accesibilidad (baja prioridad)

### 📊 Estado General:
**El proyecto está en un estado funcional y listo para producción**, con mejoras incrementales recomendadas según las prioridades identificadas. La arquitectura es sólida y permite escalabilidad futura.

**Recomendación principal:** Implementar las mejoras de alta prioridad (notificaciones, backup, integridad referencial, importación) para consolidar la aplicación antes de expandir funcionalidades.

---

---

## 🔐 Implementación RBAC y Row-Level Security (Nuevo)

### Resumen de Implementación
Se ha implementado un sistema completo de Control de Acceso Basado en Roles (RBAC) con aislamiento de datos por usuario (Row-Level Security) para diferenciar las vistas y funcionalidades del Administrador y el Transportista Proveedor.

### Características Implementadas

#### 1. Rol Transportista
- **Permisos limitados:** Solo acceso a Dashboard y módulo de Unidades Disponibles
- **Aislamiento de datos:** Solo puede ver, editar y crear sus propias unidades
- **Restricciones:** Sin acceso a módulos de administración (P. Proveedores, Altas, Clientes, Servicios, Transportistas, Tarifas)

#### 2. Row-Level Security (RLS)
- **Filtrado automático:** Todas las consultas para transportistas incluyen filtro por `user_id`
- **Validaciones de propiedad:** Verificación antes de editar/eliminar recursos
- **Scope implementado:** `scopeForUser()` en modelo UnidadDisponible

#### 3. Creación Automática de Usuarios
- **Generación automática:** Al dar de alta un proveedor, se crea automáticamente un usuario
- **Email inteligente:** Usa email del proveedor si existe, o genera uno automático
- **Contraseña temporal:** Genera contraseña aleatoria de 12 caracteres
- **Visualización:** Modal con credenciales al dar de alta, sección permanente en vista de detalles

#### 4. Cambios en Modelos
- **UnidadDisponible:** Agregado campo `user_id` y relación con User
- **TransporteProveedor:** Agregado campo `user_id` y relación con User
- **User:** Agregadas relaciones con TransporteProveedor y UnidadDisponible

#### 5. Cambios en Controladores
- **UnidadesController:** Implementado filtrado y validaciones de Row-Level Security
- **AltasProveedoresController:** Implementada creación automática de usuarios

#### 6. Navegación Actualizada
- Módulos se muestran según permisos del usuario
- Transportistas solo ven "Unidades D." y "Dashboard"
- Módulos de administración ocultos para transportistas

### Documentación Relacionada
Ver `IMPLEMENTACION_RBAC_TRANSPORTISTA.md` para documentación completa y detallada de la implementación.

---

**Generado por:** Análisis Automático del Proyecto  
**Última actualización:** 2025-01-27  
**Versión:** 2.0 (Incluye RBAC y Row-Level Security)

