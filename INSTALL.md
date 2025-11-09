# 🚀 Guía de Instalación - Sistema Atlas

## Requisitos Previos

Antes de comenzar, asegúrate de tener instalado:

- **Docker Desktop** (Windows/Mac) o **Docker Engine** (Linux)
- **Docker Compose**

Para verificar:
```bash
docker --version
docker-compose --version
```

---

## Instalación Rápida

### Paso 1: Clonar el proyecto
```bash
git clone https://github.com/tu-usuario/atlas.git
cd atlas
```

### Paso 2: Levantar los contenedores
```bash
cd docker
docker-compose up -d --build
```

⏱️ **Espera 30-60 segundos** mientras MySQL inicializa la base de datos.

### Paso 3: Verificar que todo esté corriendo
```bash
docker-compose ps
```

Deberías ver 3 contenedores en estado "Up":
- ✅ `atlas_php`
- ✅ `atlas_mysql`
- ✅ `atlas_phpmyadmin`

---

## 🌐 Acceso

### Aplicación Web
**URL:** http://localhost:8080

### phpMyAdmin
**URL:** http://localhost:8081  
**Usuario:** `root`  
**Password:** `atlas_root_2024`

---

## 👤 Usuarios de Prueba

Una vez en http://localhost:8080/login, usa cualquiera de estos usuarios:

| Email | Password | Rol |
|-------|----------|-----|
| admin@atlas.sena | admin123 | Administrador |
| portero@atlas.sena | portero123 | Portería |
| maria.lopez@sena.edu.co | instructor123 | Instructor |
| juan.perez@sena.edu.co | aprendiz123 | Aprendiz |

---

## 🔧 Comandos Útiles

### Ver logs
```bash
docker-compose logs -f
```

### Detener el sistema
```bash
docker-compose down
```

### Reiniciar el sistema
```bash
docker-compose restart
```

### Reinicio completo (⚠️ borra la base de datos)
```bash
docker-compose down -v
docker-compose up -d --build
```

---

## ❓ Solución de Problemas

### El puerto 8080 está ocupado

Edita `docker/docker-compose.yml` y cambia:
```yaml
php-apache:
  ports:
    - "8090:80"  # Cambia 8080 por otro puerto
```

### No se puede conectar a MySQL

Espera unos segundos más. MySQL tarda en inicializar.

Verifica los logs:
```bash
docker logs atlas_mysql --tail 50
```

Deberías ver: `ready for connections`

### Permisos de carpetas (Linux/Mac)

```bash
chmod -R 777 storage/logs
chmod -R 777 public/uploads
```

---

## 📊 Verificación

### Ver tablas de la base de datos
```bash
docker exec atlas_mysql mysql -uroot -patlas_root_2024 -e "USE atlas_db; SHOW TABLES;"
```

Deberías ver 9 tablas:
- anomalias
- codigos_qr
- configuracion_horario
- equipos
- imagenes_equipo
- registros_acceso
- roles
- sesiones
- usuarios

### Ver usuarios de prueba
```bash
docker exec atlas_mysql mysql -uroot -patlas_root_2024 -e "USE atlas_db; SELECT email, nombres FROM usuarios;"
```

---

## ✅ Listo!

Si todo está correcto, deberías poder:
1. ✅ Acceder a http://localhost:8080
2. ✅ Iniciar sesión con cualquier usuario de prueba
3. ✅ Ver el dashboard con estadísticas
4. ✅ Acceder a phpMyAdmin en http://localhost:8081

---

**¿Problemas?** Revisa el archivo `README.md` para más información.
