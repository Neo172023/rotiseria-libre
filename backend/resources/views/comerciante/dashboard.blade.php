<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Tablero · {{ $comercio->nombre }} · Rotisería Libre</title>
<link rel="stylesheet" href="{{ asset('css/comerciante.css') }}">
<style>
.metrics{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:14px}
.metric{padding:20px;border-radius:14px;background:white;border:1px solid #eee3d8}
.metric strong{display:block;font-size:clamp(1.6rem,3vw,2.2rem);margin-top:10px}
.metric span{font-size:.92rem;color:#6d6158}
.quick{display:flex;flex-wrap:wrap;gap:10px}
.order-row{display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px 0;border-bottom:1px solid #eee3d8}
.order-row:last-child{border-bottom:0}
@media(max-width:800px){.metrics{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:440px){.metrics{grid-template-columns:1fr 1fr}.metric{padding:14px}.quick .btn{width:100%;text-align:center}.order-row{align-items:flex-start}}
</style>
</head>
<body>
<main class="wrap">
<header class="top">
<a class="back" href="/comerciante">← Mis comercios</a>
<span class="brand">🍕 ROTISERÍA LIBRE</span>
</header>
<h1 class="heading">Tablero de {{ $comercio->nombre }}</h1>
<p class="subtitle muted">Resumen operativo · {{ now()->format('d/m/Y') }}</p>
<section class="metrics" aria-label="Indicadores del comercio">
<div class="metric"><span>Pedidos nuevos</span><strong>{{ $resumen['nuevos'] }}</strong></div>
<div class="metric"><span>En preparación</span><strong>{{ $resumen['preparando'] }}</strong></div>
<div class="metric"><span>Listos</span><strong>{{ $resumen['listos'] }}</strong></div>
<div class="metric"><span>Pedidos de hoy</span><strong>{{ $resumen['pedidos_hoy'] }}</strong></div>
</section>
<section class="panel">
<h2>Ventas del día</h2>
<p class="amount">$ {{ number_format((float) $resumen['ventas_hoy'], 2, ',', '.') }}</p>
<p class="muted small">Suma de pedidos creados hoy y marcados como «entregado». No representa cobros verificados ni descuenta gastos o devoluciones.</p>
</section>
<section class="panel">
<h2>Accesos rápidos</h2>
<div class="quick">
<a class="btn" href="/comerciante/{{ $comercio->id }}/pedidos">Ver todos los pedidos</a>
@if(DB::table('comercio_usuarios')->where('comercio_id', $comercio->id)->where('user_id', auth()->id())->where('rol', 'propietario')->exists())
<a class="btn secondary" href="/comerciante/{{ $comercio->id }}/productos">Administrar productos ({{ $productos }})</a>
@endif
</div>
</section>
<section class="panel">
<div class="cardhead"><h2>Últimos pedidos</h2><a href="/comerciante/{{ $comercio->id }}/pedidos">Ver todos</a></div>
@forelse($recientes as $pedido)
<div class="order-row">
<div>
<strong>Pedido #{{ $pedido->id }}</strong>
<div class="muted small">{{ $pedido->cliente_nombre }} · {{ $pedido->created_at }}</div>
</div>
<div style="text-align:right">
<span class="badge {{ $pedido->estado }}">{{ ucfirst($pedido->estado) }}</span>
<div style="margin-top:8px;font-weight:750">$ {{ number_format((float) $pedido->total, 2, ',', '.') }}</div>
</div>
</div>
@empty
<p class="empty">Todavía no hay pedidos.</p>
@endforelse
</section>
<p class="muted small">Los indicadores se actualizan al recargar esta página.</p>
</main>
</body>
</html>