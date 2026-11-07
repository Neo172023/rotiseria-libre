# Checkout remoto: piloto

La tienda de demostración sigue enviando pedidos únicamente por WhatsApp.

En modo remoto, usando `?api=https://TU-BACKEND&comercio=slug`, la tienda consulta el catálogo Laravel y, al confirmar, envía `POST /api/comercios/{slug}/pedidos` con identificadores de productos y cantidades. El backend valida el comercio habilitado y calcula los precios. El navegador muestra el número y total devueltos por el servidor y ofrece avisar por WhatsApp opcionalmente.

**Limitaciones antes de producción:**

- Requiere backend Laravel instalado, base MySQL y CORS configurado; no se probó en un navegador real ni en un servidor.
- No admite pizzas mitad y mitad ni artículos personalizados en el checkout remoto.
- El servidor calcula delivery como $0, por lo que **no se deben aceptar pedidos de delivery reales hasta configurar el costo de envío**.
- Falta protección de duplicados (idempotencia), controles antispam, política de privacidad, estados de pago y notificaciones.
- Si la respuesta se pierde después de registrar el pedido, el cliente podría reintentarlo; por eso la interfaz pide consultar con el comercio antes de reenviar.
- No se debe publicar con `APP_DEBUG=true` ni sin HTTPS.
