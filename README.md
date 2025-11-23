# 🎓 Sistema Atlas - Control de Acceso de Equipos

Sistema web de control de acceso de equipos para instituciones educativas del SENA. Permite gestionar el registro, seguimiento y control de entrada y salida de equipos electrónicos mediante códigos QR y verificación manual.

## 📋 Características

- ✅ **Gestión de Usuarios**: Sistema de roles (admin, administrativo, instructor, aprendiz, civil, portería)
- ✅ **Registro de Equipos**: Control completo con imágenes y códigos QR (FASE 3 COMPLETADA)
- ✅ **Gestión de Imágenes**: Subida múltiple, redimensionamiento automático, gestión de galería
- ✅ **Códigos QR**: Generación automática de códigos QR únicos por equipo
- 🚧 **Control de Acceso**: Registro de entradas/salidas (Fase 4)
- 🚧 **Detección de Anomalías**: Sistema de alertas (Fase 4)
- 🚧 **Configuración de Horarios**: Control basado en horarios (Fase 5)
- 🚧 **Reportes**: Generación de reportes (Fase 6)
- ✅ **RBAC**: Control de acceso basado en roles y permisos

## 🛠️ Stack Tecnológico

- **Backend**: PHP 8.2 nativo (sin frameworks)
- **Base de Datos**: MySQL 8.0
- **Servidor Web**: Apache 2.4
- **Arquitectura**: MVC nativa
- **Containerización**: Docker + Docker Compose

## 📦 Requisitos

- [Docker](https://www.docker.com/get-started) (versión 20.10 o superior)
- [Docker Compose](https://docs.docker.com/compose/install/) (versión 2.0 o superior)

## 🚀 Instalación

### 1. Clonar el repositorio
```bash
git clone https://github.com/Miguel12-ares/atlas.git
cd atlas
```

### 2. Levantar los contenedores
```bash
cd docker
docker-compose up -d --build
```

Este comando:
- Construye la imagen PHP con Apache
- Inicia MySQL con la base de datos
- Inicia phpMyAdmin
- Carga automáticamente el esquema y datos de prueba

### 3. Acceder a la aplicación

| Servicio | URL | Credenciales |
|----------|-----|--------------|
| **Aplicación** | http://localhost:8080 | Ver usuarios abajo |
| **phpMyAdmin** | http://localhost:8081 | Usuario: `root`<br>Password: `atlas_root_2024` |

### 👤 Usuarios de Prueba

| Email | Password | Rol |
|-------|----------|-----|
| admin@atlas.sena | admin123 | Administrador |
| portero@atlas.sena | portero123 | Portería |
| maria.lopez@sena.edu.co | instructor123 | Instructor |
| juan.perez@sena.edu.co | aprendiz123 | Aprendiz |

## 📁 Estructura del Proyecto

```
atlas/
├── docker/                 # Configuración Docker
├── src/                    # Código fuente
│   ├── config/            # Configuración
│   ├── Core/              # Clases núcleo (MVC)
│   ├── Controllers/       # Controladores
│   ├── Models/            # Modelos
│   └── Views/             # Vistas
├── public/                 # Archivos públicos
│   ├── index.php          # Punto de entrada
│   ├── assets/            # CSS, JS, imágenes
│   └── uploads/           # Archivos subidos
├── database/               # Scripts SQL
│   ├── 01-schema.sql      # Esquema (9 tablas)
│   └── 02-seeds.sql       # Datos de prueba
└── storage/                # Logs
```

## 🗄️ Base de Datos

9 tablas en Tercera Forma Normal (3NF):
- ✅ **roles** - Catálogo de roles del sistema
- ✅ **usuarios** - Información de usuarios
- ✅ **equipos** - Registro de equipos electrónicos (FASE 3)
- ✅ **imagenes_equipo** - Almacenamiento de imágenes (FASE 3)
- ✅ **codigos_qr** - Códigos QR generados (FASE 3)
- 🚧 **registros_acceso** - Registro de entradas/salidas (Fase 4)
- 🚧 **anomalias** - Detección de anomalías (Fase 4)
- 🚧 **configuracion_horario** - Configuración de horarios (Fase 5)
- ✅ **sesiones** - Gestión de sesiones

## 🔧 Comandos Útiles

### Docker
```bash
# Iniciar
docker-compose up -d

# Detener
docker-compose down

# Ver logs
docker-compose logs -f

# Reiniciar
docker-compose restart
```

### Base de Datos
```bash
# Backup
docker exec atlas_mysql mysqldump -uroot -patlas_root_2024 atlas_db > backup.sql

# Restaurar
docker exec -i atlas_mysql mysql -uroot -patlas_root_2024 atlas_db < backup.sql

# Acceder a MySQL CLI
docker exec -it atlas_mysql mysql -uroot -patlas_root_2024 atlas_db
```

## 🔐 Seguridad

- ✅ Contraseñas hasheadas con bcrypt
- ✅ PDO prepared statements
- ✅ Validación y sanitización
- ✅ Control de acceso por roles (RBAC)
- ✅ Sesiones seguras
- ✅ Headers de seguridad

## 📈 Estado del Proyecto

### Fases Completadas

#### ✅ Fase 1: Configuración Inicial
- Configuración de Docker
- Esquema de base de datos
- Arquitectura MVC nativa
- Sistema de rutas

#### ✅ Fase 2: Autenticación y Roles
- Sistema de login/registro
- Gestión de sesiones
- RBAC completo
- Middleware de permisos
- Dashboard funcional

#### ✅ Fase 3: Gestión de Equipos (COMPLETADA - 19/11/2025)
- ✅ Modelo Equipo con CRUD completo
- ✅ Modelo ImagenEquipo
- ✅ Modelo CodigoQR
- ✅ Formulario de registro con validación
- ✅ Subida múltiple de imágenes (max 5)
- ✅ Redimensionamiento automático de imágenes
- ✅ Generador de códigos QR
- ✅ Vista de listado con filtros
- ✅ Vista de detalle de equipo
- ✅ Edición de equipos
- ✅ Eliminación suave (soft delete)
- ✅ Sistema de permisos por propietario/admin

Ver documentación completa en: [`docs/FASE_3_COMPLETADA.md`](docs/FASE_3_COMPLETADA.md)

#### 🚧 Próximas Fases
- **Fase 4**: Sistema de Registro de Accesos
- **Fase 5**: Códigos QR y Detección de Anomalías
- **Fase 6**: Reportes y Configuración de Horarios

## 🎨 Colores del SENA

El sistema utiliza la paleta de colores institucional del SENA:
- **Verde principal**: #39A900
- **Verde claro**: #5DBF1A
- **Verde oscuro**: #2D8400
- **Fondo pálido**: #E8F5E0

## 📝 Licencia

Proyecto privado para uso interno del SENA.

## 👥 Créditos

Desarrollado para SENA Colombia  
Versión 1.0.0

---

**¿Necesitas ayuda?** Revisa el archivo `INSTALL.md` para más detalles de instalación.
