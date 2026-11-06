# Rotisería Libre — Primera versión demostrativa

Base pública personalizable para rotiserías. Inspirada en la organización modular de Brasas del Estero, sin modificar el repositorio original.

## Funciona ahora
- Menú digital con productos de ejemplo (precios ilustrativos).
- Carrito con varios productos y cantidades.
- Retiro o delivery con costo configurable.
- Formulario de cliente y envío manual del resumen por WhatsApp.
- Configuración local del nombre, lema, WhatsApp, costo de reparto y productos.
- Diseño adaptable a celulares.

## Limitaciones importantes
- Los datos se guardan **solo en el navegador** mediante localStorage. Otros dispositivos no ven esos cambios.
- El pedido **no se guarda en un servidor** ni aparece en un panel compartido.
- El cliente debe confirmar el envío en WhatsApp; no hay mensajes automáticos.
- No hay autenticación ni pagos online. No usar esta demo como sistema de producción.
- La imagen del menú de ejemplo no se redistribuye; el comerciante debe cargar sus propios contenidos.

## Probar
Servir la carpeta por HTTP local o activar GitHub Pages desde Settings > Pages, branch main después de fusionar el PR. Para probar sin instalar dependencias: `python -m http.server 4173` y abrir http://localhost:4173/.

## Próxima fase
Backend Laravel/MySQL, autenticación del comercio, API de catálogo, pedidos persistidos, panel sincronizado, estados, auditoría, configuración por comercio y pruebas automatizadas. La API debe validar precios, totales y permisos del lado servidor.

## Publicación y licencia
Antes de distribuir como software abierto, agregar una licencia explícita (MIT propuesta) y revisar los derechos de cualquier código o recurso reutilizado.
