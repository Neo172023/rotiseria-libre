@php
    $base = '/comerciante/'.$comercio->id;
    $actual = request()->path();
    $esPropietario = \Illuminate\Support\Facades\DB::table('comercio_usuarios')
        ->where('comercio_id', $comercio->id)
        ->where('user_id', auth()->id())
        ->where('rol', 'propietario')
        ->exists();
    $enlace = static fn (string $ruta): string => $actual === ltrim($ruta, '/') ? 'activo' : '';
@endphp
<style>
.navegacion-comerciante{display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin:16px 0 24px;padding:10px;border:1px solid #e9dfd2;border-radius:14px;background:#fff}
.navegacion-comerciante a{display:inline-flex;align-items:center;justify-content:center;gap:5px;padding:10px 13px;border-radius:9px;color:#4a3028;text-decoration:none;font-weight:600;font-size:.94rem}
.navegacion-comerciante a:hover{background:#f5ede5}
.navegacion-comerciante a.activo{background:#713727;color:#fff}
.navegacion-comerciante a:focus-visible{outline:3px solid #c66c35;outline-offset:2px}
@media(max-width:600px){.navegacion-comerciante{display:grid;grid-template-columns:repeat(2,minmax(0,1fr))}.navegacion-comerciante a{padding:11px 7px;font-size:.86rem;text-align:center}}
</style>
<nav class="navegacion-comerciante" aria-label="Secciones del comercio">
    <a href="{{ $base.'/tablero' }}" class="{{ $enlace($base.'/tablero') }}" @if($enlace($base.'/tablero')) aria-current="page" @endif>📊 Tablero</a>
    <a href="{{ $base.'/pedidos' }}" class="{{ $enlace($base.'/pedidos') }}" @if($enlace($base.'/pedidos')) aria-current="page" @endif>🧾 Pedidos</a>
    @if($esPropietario)
    <a href="{{ $base.'/productos' }}" class="{{ $enlace($base.'/productos') }}" @if($enlace($base.'/productos')) aria-current="page" @endif>🥪 Productos</a>
    @endif
    <a href="{{ $base.'/movimientos-stock' }}" class="{{ $enlace($base.'/movimientos-stock') }}" @if($enlace($base.'/movimientos-stock')) aria-current="page" @endif>📦 Historial de stock</a>
    <a href="/comerciante">🏠 Mis comercios</a>
</nav>
