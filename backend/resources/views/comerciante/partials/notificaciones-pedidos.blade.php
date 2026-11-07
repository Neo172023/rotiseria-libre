<section class="panel" aria-label="Avisos de pedidos"><div style="display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap"><div><strong>🔔 Avisos de nuevos pedidos</strong><p id="notificaciones-estado" class="muted small" role="status" aria-live="polite">Comprobación automática cada 8 segundos mientras esta pantalla esté abierta.</p></div><button type="button" class="btn secondary" id="notificaciones-sonido">🔕 Activar sonido</button></div></section>
<script>
(() => {
 const comercioId = @json($comercio->id);
 const url = '/comerciante/' + comercioId + '/novedades-pedidos';
 const boton = document.getElementById('notificaciones-sonido');
 const aviso = document.getElementById('notificaciones-estado');
 const estadoInicial = @json((int) ($ultimoPedidoId ?? 0));
 let ultimo = estadoInicial;
 let iniciado = false;
 let sonidoActivo = false;
 let audio = null;
 function beep() {
   if (!sonidoActivo || !audio) return;
   try {
     if (audio.state === 'suspended') audio.resume().catch(() => {});
     const osc = audio.createOscillator(), gain = audio.createGain();
     osc.frequency.setValueAtTime(880, audio.currentTime);
     osc.frequency.setValueAtTime(660, audio.currentTime + .14);
     gain.gain.setValueAtTime(.07, audio.currentTime);
     gain.gain.exponentialRampToValueAtTime(.001, audio.currentTime + .32);
     osc.connect(gain); gain.connect(audio.destination);
     osc.start(); osc.stop(audio.currentTime + .33);
   } catch (_) {}
 }
 boton?.addEventListener('click', async () => {
   if (sonidoActivo) {
     sonidoActivo = false;
     boton.textContent = '🔕 Activar sonido';
     return;
   }
   try {
     audio ??= new (window.AudioContext || window.webkitAudioContext)();
     await audio.resume();
     sonidoActivo = true;
     boton.textContent = '🔔 Desactivar sonido';
     beep();
   } catch (_) {
     aviso.textContent = 'El navegador no permitió reproducir sonido.';
   }
 });
 async function consultar() {
   if (document.hidden) return;
   try {
     const respuesta = await fetch(url, {credentials:'same-origin',cache:'no-store',headers:{'Accept':'application/json'}});
     if (!respuesta.ok) return;
     const datos = await respuesta.json();
     const id = Number(datos.ultimo_id || 0);
     if (!iniciado) { ultimo = Math.max(ultimo, id); iniciado = true; return; }
     if (id > ultimo) {
       ultimo = id;
       beep();
       aviso.textContent = '🆕 ¡Llegó un pedido nuevo! Actualizando…';
       setTimeout(() => location.reload(), 1000);
     }
   } catch (_) { aviso.textContent = 'Sin conexión. Se volverá a intentar automáticamente.'; }
 }
 consultar();
 setInterval(consultar, 8000);
 document.addEventListener('visibilitychange', () => { if (!document.hidden) consultar(); });
})();
</script>