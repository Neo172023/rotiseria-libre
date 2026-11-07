# Instalación local (Laravel 12)

**En desarrollo: no desplegar en producción.** Requiere PHP 8.2+, Composer, MySQL y extensiones PHP correspondientes.

1. En `backend/`, ejecutar `composer install`.
2. Copiar `.env.example` a `.env` y ejecutar `php artisan key:generate`.
3. Crear la base MySQL `rotiseria_libre` y configurar las credenciales en `.env`.
4. Ejecutar `php artisan migrate`.
5. Ejecutar `php artisan rotiseria:crear-superadmin` (solicita nombre, correo y contraseña; no hay claves precargadas).
6. Ejecutar `php artisan serve` y visitar `http://127.0.0.1:8000/ingresar`.
7. Probar con `php artisan test` (requiere extensión SQLite para pruebas).

### Alcance
- Inicio y cierre de sesión web con CSRF, regeneración de sesión y limitación de intentos.
- Solo superadministrador puede listar, crear y cambiar estado de comercios.
- Los nuevos comercios nacen pendientes.
- No existe registro público de comercios.
- **No hay todavía panel de comerciante, creación de cuentas de comerciantes, catálogo por API, pedidos ni sincronización con GitHub Pages.**
- No se han ejecutado pruebas ni verificado instalación en este entorno.
- Antes de producción: revisión de seguridad, auditoría, control de privilegios, pruebas y configuración de HTTPS, sesiones y despliegue.
