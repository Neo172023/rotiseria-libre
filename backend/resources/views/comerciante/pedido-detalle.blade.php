<!doctype html>
<html lang="es"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pedido #{{ $detalle->id }} · {{ $comercio->nombre }}</title>
<link rel="stylesheet" href="{{ asset('css/comerciante.css') }}">
<style>
.detalle-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
.detalle-grid dt{font-size:.86rem;color:#6d6158;margin-bottom:4px}
.detalle-grid dd{margin:0;overflow-wrap:anywhere;font-weight:600}
.detalle-tabla{width:100%;border-collapse:collapse}
.detalle-tabla th,.detalle-tabla td{padding:12px 8px;text-align:left;border-bottom:1px solid #eee3d8}
.detalle-tabla .numero{text-align:right;white-space:nowrap}
@media(max-width:600px){.detalle-grid{grid-template-columns:1fr}.detalle-tabla th,.detalle-tabla td{padding:10px 4px;font-size:.85rem}}
</style></head>
<body><main class="wrap">
<header class="top"><a class="back" href="/comerciante/{{ $comercio->id }}/pedidos">← Volver a pedidos</a><span class="brand">🍕 ROTISERÍA LIBRE</span></header>
@include('comerciante.partials.navegacion')
<h1 class="heading">Pedido #{{ $detalle->id }}</h1>
<p class="subtitle muted">{{ $comercio->nombre }} · {{ \Illuminate\Support\Carbon::parse($detalle->created_at)->format('d/m/Y H:i') }}</p>
@if(session('ok'))<p class="notice" role="status">{{ session('ok') }}</p>@endif
@if($errors->any())<ul class="errors" role="alert">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>@endif
<section class="panel"><h2>Datos del pedido</h2>
<dl class="detalle-grid">
<div><dt>Cliente</dt><dd>{{ $detalle->cliente_nombre }}</dd></div>
<div><dt>Teléfono</dt><dd>{{ $detalle->cliente_telefono ?: 'No informado' }}</dd></div>
<div><dt>Modalidad</dt><dd>{{ $detalle->modalidad === 'delivery' ? 'Envío a domicilio' : 'Retiro en el local' }}</dd></div>
<div><dt>Dirección de entrega</dt><dd>{{ $detalle->modalidad === 'delivery' ? ($detalle->direccion ?: 'No informada') : 'No corresponde' }}</dd></div>
<div><dt>Forma de pago</dt><dd>{{ ucfirst($detalle->pago) }}</dd></div>
<div><dt>Estado actual</dt><dd><span class="badge {{ $detalle->estado }}">{{ ucfirst($detalle->estado) }}</span></dd></div>
</dl>
<h3>Observaciones del cliente</h3>
<p style="white-space:pre-wrap;overflow-wrap:anywhere">{{ $detalle->notas ?: 'Sin observaciones.' }}</p>
</section>
<section class="panel"><h2>Productos solicitados</h2><div style="overflow-x:auto">
<table class="detalle-tabla"><thead><tr><th>Producto</th><th class="numero">Cantidad</th><th class="numero">Precio unitario</th><th class="numero">Subtotal</th></tr></thead><tbody>
@forelse($items as $item)
<tr><td>{{ $item->descripcion }}
@if($item->opciones)
<div class="muted small">{{ is_array(json_decode($item->opciones, true)) ? implode(', ', array_map(fn ($valor) => is_scalar($valor) ? (string) $valor : json_encode($valor, JSON_UNESCAPED_UNICODE), json_decode($item->opciones, true))) : $item->opciones }}</div>
@endif
</td><td class="numero">{{ $item->cantidad }}</td><td class="numero">$ {{ number_format((float)$item->precio_unitario,2,',','.') }}</td><td class="numero">$ {{ number_format((float)$item->total,2,',','.') }}</td></tr>
@empty<tr><td colspan="4">No hay productos registrados.</td></tr>@endforelse
</tbody></table></div>
<p class="muted">Subtotal: $ {{ number_format((float)$detalle->subtotal,2,',','.') }}</p>
<p class="muted">Envío: $ {{ number_format((float)$detalle->costo_envio,2,',','.') }}</p>
<p class="amount">Total: $ {{ number_format((float)$detalle->total,2,',','.') }}</p>
</section>
<section class="panel"><h2>Gestionar pedido</h2>
<form class="inlineform" method="POST" action="/comerciante/{{ $comercio->id }}/pedidos/{{ $detalle->id }}/estado">
@csrf @method('PATCH')
<label class="field" for="estado-detalle">Estado
<select id="estado-detalle" name="estado">
@foreach(['recibido','preparando','listo','enviado','entregado','cancelado'] as $estado)
<option value="{{ $estado }}" @selected($detalle->estado === $estado)>{{ ucfirst($estado) }}</option>
@endforeach
</select></label>
<button class="btn" type="submit">Guardar estado</button>
</form>
<p class="muted small">Al cancelar, el sistema devuelve automáticamente las unidades al inventario.</p>
</section>
</main></body></html>