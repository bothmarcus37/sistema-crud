# Registro de clientes

Sistema CRUD en PHP y MySQL para llevar el control de los clientes de un servicio web.

## Qué hace
- Alta de clientes con nombre, correo, tipo de servicio y estado.
- Tabla con búsqueda por nombre o correo y filtro por estado.
- Edición y baja de cada cliente.
- Resumen con el total de clientes por estado.

## Cómo ejecutarlo
1. Copia la carpeta a `htdocs` de XAMPP e inicia Apache y MySQL.
2. Abre `conexion.php` y pon el usuario y la contraseña de tu MySQL.
3. Abre `http://localhost/sistema_crud/`.

La base `gestion_web` y la tabla `clientes` se crean solas la primera vez (también están en `database.sql`).

## Seguridad
- Consultas preparadas en todas las operaciones (sin inyección SQL).
- Los datos se validan en el servidor y se escapan al mostrarse.
- Borrar y editar solo funcionan por POST.

## Hecho con
PHP, MySQL, HTML, CSS y JavaScript.
