# Conexión de tienda pública a catálogo Laravel (piloto)

La tienda estática de GitHub Pages sigue mostrando la demo local por defecto.

Para consultar un comercio real habilitado, abrir la tienda con dos parámetros:

```text
https://neo172023.github.io/rotiseria-libre/?api=https%3A%2F%2FAPI-EJEMPLO.com&comercio=mi-rotiseria
```

- `api`: URL base **HTTPS** del backend Laravel (o `http://localhost:8000` para pruebas locales).
- `comercio`: slug de un comercio habilitado.
- El navegador consulta `GET /api/comercios/{slug}/catalogo` y muestra únicamente ese catálogo.
- Si el comercio está suspendido, la API responde 404 y la tienda no muestra productos de demostración.
- Si el servidor no está disponible, no se permite hacer pedidos sobre precios desactualizados.
- El carrito y el envío por WhatsApp siguen siendo locales: **no hay persistencia de pedidos en MySQL**.
- La tienda no utiliza todavía el costo de envío ni el logo del backend.
- Los productos remotos se muestran bajo la categoría general Productos hasta exponer las categorías por API.
- La funcionalidad de pizzas mitad y mitad depende de categorías que todavía no están expuestas por la API, por lo que en este piloto no se habilita para productos remotos.
- Se requiere configurar CORS en el servidor para permitir el origen de la tienda GitHub Pages.
- No se debe publicar el backend con `APP_DEBUG=true` ni sin HTTPS.

**Estado:** código subido, sin pruebas en navegador ni servidor Laravel desplegado.
