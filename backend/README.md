# Rotisería Libre — backend multicomercio (etapa inicial)

**Estado: esquema de base de datos, NO backend operativo.** Estos archivos son migraciones para incorporar a un proyecto Laravel 12; todavía no hay proyecto Laravel instalable, autenticación, API, ni panel privado.

## Decisiones de acceso

- Solo el **superadministrador** podrá crear, habilitar o suspender comercios y asignar sus usuarios.
- No existirá registro público de comercios.
- Los comerciantes podrán gestionar exclusivamente sus propios comercios habilitados.
- Los clientes podrán consultar catálogos públicos de comercios habilitados sin iniciar sesión.
- La suspensión impedirá nuevos pedidos, pero conservará los datos para auditoría.
- Nunca confiar en un `comercio_id` enviado por el navegador para autorizar acciones. Obtenerlo de la sesión autenticada y comprobar pertenencia en cada consulta.
- **El rol superadministrador debe implementarse en el backend**, con autorización por políticas/middleware; no alcanza con ocultar botones.

## Migraciones incluidas

1. `comercios`: slug único, estado pendiente/habilitado/suspendido y eliminación lógica.
2. `comercio_usuarios`: pertenencia de usuarios a comercios, rol propietario/empleado.
3. `categorias` y `productos`: datos aislados por comercio; la clave foránea compuesta evita categorías de otro comercio.
4. `pedidos` y `pedido_items`: estados, modalidad, pago, importes y opciones de pizzas.

Las migraciones suponen que existe la tabla estándar `users` de Laravel. No ejecutarlas en producción sin revisar integridad, índices y estrategia de backups.

## Próximos pasos

1. Inicializar un proyecto Laravel 12 completo en `backend/`, con `composer.json`, `artisan`, configuración y pruebas.
2. Implementar superadministrador inicial por comando seguro (sin contraseñas precargadas), login y permisos del servidor.
3. API pública por slug para comercios habilitados; API privada para comerciantes y superadministrador.
4. Validación de pedidos, precios y ofertas en el servidor, con transacciones y comprobación de comercio.
5. Conectar frontend existente sin exponer la administración local como panel seguro.
6. Tests de aislamiento entre comercios, suspensión, autenticación y creación exclusiva de comercios.

**No se ha ejecutado Laravel ni se han corrido migraciones o pruebas en esta etapa.**
