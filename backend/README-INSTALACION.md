# Instalación local (Laravel 12)

Este es un **esqueleto inicial**, no un panel listo para producción. Requiere PHP 8.2+, Composer y MySQL. Todavía no tiene login, políticas, controladores de comercios ni API de pedidos.

1. Abrir terminal en `backend/`.
2. Ejecutar `composer install` (genera `composer.lock` localmente).
3. Copiar `.env.example` a `.env`.
4. Ejecutar `php artisan key:generate`.
5. Crear una base MySQL vacía llamada `rotiseria_libre` y configurar credenciales en `.env`.
6. Ejecutar `php artisan migrate`.
7. Ejecutar `php artisan serve` y abrir `http://127.0.0.1:8000/api/estado`.

**No usar credenciales reales en GitHub.** No ejecutar en un servidor público con `APP_DEBUG=true`. La creación exclusiva de comercios por superadministrador todavía **no está implementada**; es el siguiente desarrollo.

### Pendientes
- Login seguro y bootstrap del superadministrador sin claves precargadas.
- Políticas, control de comercios habilitados, aislamiento de tenants y pruebas.
- API pública de catálogo, API privada y pedidos.
- Revisión de migraciones y pruebas de instalación real.
