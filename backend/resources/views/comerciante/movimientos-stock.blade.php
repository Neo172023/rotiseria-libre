<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Historial de stock · {{ $comercio->nombre }}</title>
<link rel="stylesheet" href="{{ asset('css/comerciante.css') }}"></head>
<body><main class="wrap">
<header class="top"><a class="back" href="/comerciante/{{ $comercio->id }}/productos">← Volver a productos</a><span class="brand">🍕 ROTISERÍA LIBRE</span></header>
<h1 class="heading">Historial de movimientos de stock</h1>
<p class="subtitle muted">{{ $comercio->nombre }} · Ventas, cancelaciones y ajustes</p>
<section class="panel" style="overflow-x:auto"><table style="width:100%;border-collapse:collapse;text-align:left">
<thead><tr><th>Fecha</th><th>Producto</th><th>Movimiento</th><th>Cambio</th><th>Antes</th><th>Después</th><th>Usuario</th><th>Pedido</th></tr></thead>
<tbody>@forelse($movimientos as $m)
<tr><td>{{ $m->created_at }}</td><td>{{ $m->producto_nombre }}</td><td>{{ $m->tipo }}<br><small>{{ $m->motivo }}</small></td>
<td>{{ $m->cantidad_cambio > 0 ? '+' : '' }}{{ $m->cantidad_cambio }}</td>
<td>{{ $m->stock_anterior ?? 'Sin límite' }}</td><td>{{ $m->stock_nuevo ?? 'Sin límite' }}</td>
<td>{{ $m->usuario_nombre ?? 'Cliente / sistema' }}</td><td>{{ $m->pedido_id ?? '—' }}</td></tr>
@empty<tr><td colspan="8">Todavía no hay movimientos registrados desde la activación de este historial.</td></tr>@endforelse</tbody></table>
<div class="pagination">{{ $movimientos->links() }}</div></section></main></body></html>
