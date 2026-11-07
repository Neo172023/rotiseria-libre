<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Pedidos · {{ $comercio->nombre }} · Rotisería Libre</title>
<link rel="stylesheet" href="{{ asset('css/comerciante.css') }}">
</head>
<body>
<main class="wrap">
<header class="top">
<a class="back" href="/comerciante">← Volver a mis comercios</a>
<span class="brand">🍕 ROTISERÍA LIBRE</span>
</header>
@include('comerciante.partials.navegacion')
<h1 class="heading">Pedidos</h1>
<p class="subtitle muted">{{ $comercio->nombre }} · Gestioná los pedidos recibidos</p>
@if(session('ok'))
<p class="notice" role="status">{{ session('ok') }}</p>
@endif
@if($errors->any())
<ul class="errors" role="alert">
@foreach($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
@endif
@include('comerciante.partials.notificaciones-pedidos')
<section class="cards" aria-label="Pedidos registrados">
@forelse($pedidos as $pedido)
<article class="card">
<div class="cardhead">
<div>
<h3>Pedido #{{ $pedido->id }}</h3>
<p class="muted small">{{ $pedido->cliente_nombre }}</p>
</div>
<span class="badge {{ $pedido->estado }}">{{ ucfirst($pedido->estado) }}</span>
</div>
<p class="amount">$ {{ number_format((float) $pedido->total, 2, ',', '.') }}</p>
<p class="muted small">Estado actual: {{ $pedido->estado }}</p>
<form class="inlineform" method="POST" action="/comerciante/{{ $comercio->id }}/pedidos/{{ $pedido->id }}/estado">
@csrf
@method('PATCH')
<label class="field" for="estado-{{ $pedido->id }}">Cambiar estado
<select id="estado-{{ $pedido->id }}" name="estado">
@foreach(['recibido','preparando','listo','enviado','entregado','cancelado'] as $estado)
<option value="{{ $estado }}" @selected($pedido->estado === $estado)>{{ ucfirst($estado) }}</option>
@endforeach
</select>
</label>
<button class="btn" type="submit">Guardar estado</button>
</form>
</article>
@empty
<div class="panel empty">Todavía no hay pedidos para este comercio.</div>
@endforelse
</section>
<div class="pagination">{{ $pedidos->links() }}</div>
</main>
</body>
</html>