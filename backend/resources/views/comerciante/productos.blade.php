<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Productos · {{ $comercio->nombre }} · Rotisería Libre</title>
<link rel="stylesheet" href="{{ asset('css/comerciante.css') }}">
</head>
<body>
<main class="wrap">
<header class="top">
<a class="back" href="/comerciante">← Volver a mis comercios</a>
<span class="brand">🍕 ROTISERÍA LIBRE</span>
</header>
<h1 class="heading">Productos</h1>
<p class="subtitle muted">{{ $comercio->nombre }} · Administrá tu catálogo</p>
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
<section class="panel">
<h2>Agregar producto</h2>
<form method="POST" action="/comerciante/{{ $comercio->id }}/productos">
@csrf
<div class="grid">
<label class="field">Nombre <input name="nombre" required maxlength="160" value="{{ old('nombre') }}"></label>
<label class="field">Precio normal ($) <input name="precio" type="number" min="0" step="0.01" required value="{{ old('precio') }}"></label>
<label class="field full">Descripción <textarea name="descripcion" maxlength="2000">{{ old('descripcion') }}</textarea></label>
<label class="field">Precio de oferta ($) <input name="precio_oferta" type="number" min="0" step="0.01" value="{{ old('precio_oferta') }}"></label>
<label class="field">Oferta hasta (opcional) <input name="oferta_hasta" type="datetime-local" value="{{ old('oferta_hasta') }}"></label>
<label class="field">Disponible <select name="disponible"><option value="1" @selected(old('disponible','1') == '1')>Sí</option><option value="0" @selected(old('disponible') == '0')>No</option></select></label>
</div>
<div class="actions"><button class="btn" type="submit">Agregar producto</button></div>
</form>
</section>
<section class="panel">
<h2>Catálogo</h2>
<div class="cards">
@forelse($productos as $producto)
<article class="card">
<div class="cardhead">
<h3>{{ $producto->nombre }}</h3>
<span class="badge">{{ $producto->disponible ? 'Disponible' : 'No disponible' }}</span>
</div>
<p class="muted small">{{ $producto->descripcion }}</p>
<p class="amount">$ {{ number_format((float) $producto->precio, 2, ',', '.') }}</p>
@if($producto->precio_oferta !== null)
<p class="small">Oferta: <strong>$ {{ number_format((float) $producto->precio_oferta, 2, ',', '.') }}</strong></p>
@endif
<details class="edit">
<summary>Editar producto</summary>
<form method="POST" action="/comerciante/{{ $comercio->id }}/productos/{{ $producto->id }}">
@csrf
@method('PATCH')
<div class="grid" style="margin-top:14px">
<label class="field full">Nombre <input name="nombre" required maxlength="160" value="{{ $producto->nombre }}"></label>
<label class="field full">Descripción <textarea name="descripcion" maxlength="2000">{{ $producto->descripcion }}</textarea></label>
<label class="field">Precio normal ($) <input name="precio" type="number" min="0" step="0.01" required value="{{ number_format((float) $producto->precio, 2, '.', '') }}"></label>
<label class="field">Oferta ($) <input name="precio_oferta" type="number" min="0" step="0.01" value="{{ $producto->precio_oferta === null ? '' : number_format((float) $producto->precio_oferta, 2, '.', '') }}"></label>
<label class="field full">Oferta hasta <input name="oferta_hasta" type="datetime-local" value="{{ $producto->oferta_hasta?->format('Y-m-d\TH:i') }}"></label>
<label class="field">Disponible <select name="disponible"><option value="1" @selected($producto->disponible)>Sí</option><option value="0" @selected(!$producto->disponible)>No</option></select></label>
</div>
<div class="actions"><button class="btn" type="submit">Guardar cambios</button></div>
</form>
<hr class="divider">
<form method="POST" action="/comerciante/{{ $comercio->id }}/productos/{{ $producto->id }}" onsubmit="return confirm('¿Retirar este producto del catálogo?')">
@csrf
@method('DELETE')
<button class="btn danger" type="submit">Retirar producto</button>
</form>
</details>
</article>
@empty
<p class="empty">Todavía no hay productos.</p>
@endforelse
</div>
<div class="pagination">{{ $productos->links() }}</div>
</section>
</main>
</body>
</html>