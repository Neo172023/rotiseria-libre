<h1>Pedidos</h1>
@foreach($pedidos as $pedido)
<p>Pedido #{{ $pedido->id }} — {{ $pedido->cliente_nombre }} — ${{ $pedido->total }} — {{ $pedido->estado }}</p>
<form method="POST" action="/comerciante/{{ $comercio->id }}/pedidos/{{ $pedido->id }}/estado">@csrf @method('PATCH')<select name="estado">@foreach(['recibido','preparando','listo','enviado','entregado','cancelado'] as $estado)<option value="{{ $estado }}">{{ $estado }}</option>@endforeach</select><button>Guardar</button></form>
@endforeach
{{ $pedidos->links() }}
