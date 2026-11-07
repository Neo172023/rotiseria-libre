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
<h2>Logo de la rotisería</h2>
@if($comercio->logo)
<img src="{{ asset('storage/'.$comercio->logo) }}" alt="Logo de {{ $comercio->nombre }}" style="max-width:160px;max-height:120px;object-fit:contain;margin-bottom:12px">
@endif
<form method="POST" enctype="multipart/form-data" action="/comerciante/{{ $comercio->id }}/logo">
@csrf
<label class="field">Subir logo (JPG, PNG o WebP, máximo 2 MB) <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" required></label>
<div class="actions"><button class="btn" type="submit">Guardar logo</button></div>
</form>
</section>
<section class="panel">
<h2>Personalización de la tienda</h2>
<p class="muted small">Estos datos y colores se aplican solo a {{ $comercio->nombre }}.</p>
<form method="POST" action="/comerciante/{{ $comercio->id }}/identidad">
@csrf
@method('PATCH')
<div class="grid">
<label class="field full">Frase de presentación <input name="lema" maxlength="180" value="{{ old('lema', $comercio->lema) }}" placeholder="¡Rico, casero y a tu puerta!"></label>
<label class="field full">Descripción de la rotisería <textarea name="descripcion" maxlength="2000">{{ old('descripcion', $comercio->descripcion) }}</textarea></label>
<label class="field">Color de cabecera <input type="color" name="color_primario" value="{{ old('color_primario', $comercio->color_primario ?? '#662b20') }}"></label>
<label class="field">Color de botones <input type="color" name="color_boton" value="{{ old('color_boton', $comercio->color_boton ?? '#b33b22') }}"></label>
</div>
<div class="actions"><button class="btn" type="submit">Guardar personalización</button></div>
</form>
</section>
<section class="panel">
<h2>Agregar producto</h2>
<form method="POST" enctype="multipart/form-data" action="/comerciante/{{ $comercio->id }}/productos">
@csrf
<div class="grid">
<label class="field">Nombre <input name="nombre" required maxlength="160" value="{{ old('nombre') }}"></label>
<label class="field">Precio normal ($) <input name="precio" type="number" min="0" step="0.01" required value="{{ old('precio') }}"></label>
<label class="field full">Descripción <textarea name="descripcion" maxlength="2000">{{ old('descripcion') }}</textarea></label>
<label class="field">Precio de oferta ($) <input name="precio_oferta" type="number" min="0" step="0.01" value="{{ old('precio_oferta') }}"></label>
<label class="field">Oferta hasta (opcional) <input name="oferta_hasta" type="datetime-local" value="{{ old('oferta_hasta') }}"></label>
<label class="field full">Foto del producto (JPG, PNG o WebP, máximo 3 MB) <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp"></label>
<label class="field">Disponible <select name="disponible"><option value="1" @selected(old('disponible','1') == '1')>Sí</option><option value="0" @selected(old('disponible') == '0')>No</option></select></label>
</div>
<label class="field">Unidades disponibles (vacío = sin límite) <input name="stock_disponible" type="number" min="0" max="1000000" value="{{ old('stock_disponible') }}" placeholder="Ej.: 15"></label>
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
<span class="badge">{{ !$producto->disponible ? 'Oculto' : ($producto->stock_disponible === 0 ? 'Agotado' : ($producto->stock_disponible !== null && $producto->stock_disponible <= 3 ? '⚠️ Stock bajo' : 'Disponible')) }}</span>
</div>
@if($producto->imagen)
<img src="{{ asset('storage/'.$producto->imagen) }}" alt="Foto de {{ $producto->nombre }}" loading="lazy" style="width:100%;height:180px;object-fit:cover;border-radius:10px;margin:12px 0">
@endif
<p class="muted small">{{ $producto->descripcion }}</p>
<p class="small"><strong>Stock:</strong> {{ $producto->stock_disponible === null ? 'Sin límite' : $producto->stock_disponible . ' unidades' }}</p>
<p class="amount">$ {{ number_format((float) $producto->precio, 2, ',', '.') }}</p>
@if($producto->precio_oferta !== null)
<p class="small">Oferta: <strong>$ {{ number_format((float) $producto->precio_oferta, 2, ',', '.') }}</strong></p>
@endif
<details class="edit">
<summary>Editar producto</summary>
<form method="POST" enctype="multipart/form-data" action="/comerciante/{{ $comercio->id }}/productos/{{ $producto->id }}">
@csrf
@method('PATCH')
<div class="grid" style="margin-top:14px">
<label class="field full">Nombre <input name="nombre" required maxlength="160" value="{{ $producto->nombre }}"></label>
<label class="field full">Descripción <textarea name="descripcion" maxlength="2000">{{ $producto->descripcion }}</textarea></label>
<label class="field">Precio normal ($) <input name="precio" type="number" min="0" step="0.01" required value="{{ number_format((float) $producto->precio, 2, '.', '') }}"></label>
<label class="field">Oferta ($) <input name="precio_oferta" type="number" min="0" step="0.01" value="{{ $producto->precio_oferta === null ? '' : number_format((float) $producto->precio_oferta, 2, '.', '') }}"></label>
<label class="field full">Cambiar foto (opcional) <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp"></label>
<label class="field full">Oferta hasta <input name="oferta_hasta" type="datetime-local" value="{{ $producto->oferta_hasta?->format('Y-m-d\TH:i') }}"></label>
<label class="field">Disponible <select name="disponible"><option value="1" @selected($producto->disponible)>Sí</option><option value="0" @selected(!$producto->disponible)>No</option></select></label>
</div>
<label class="field">Unidades disponibles (vacío = sin límite) <input name="stock_disponible" type="number" min="0" max="1000000" value="{{ $producto->stock_disponible }}" placeholder="Sin límite"></label>
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