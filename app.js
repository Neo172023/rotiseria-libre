const initial=[{id:"pizza-comun",name:"Pizza común",category:"Pizzas",price:9500},{id:"pizza-especial",name:"Pizza especial",category:"Pizzas",price:10000},{id:"lomito",name:"Lomito común",category:"Lomitos",price:9000},{id:"sandwich",name:"Sándwich napolitano",category:"Sándwiches",price:12000},{id:"hamburguesa",name:"Hamburguesa clásica",category:"Hamburguesas",price:4000},{id:"milapizza",name:"Milapizza entera",category:"Especialidades",price:38000}];const defaults={name:"Mi Rotisería",tagline:"¡Rico, casero y a tu puerta!",phone:"",delivery:0,logo:""};const read=(k,d)=>{try{return JSON.parse(localStorage.getItem(k))??d}catch{return d}};let config=read("rl-config",defaults),products=read("rl-products",initial),cart={};const el=id=>document.getElementById(id),money=n=>new Intl.NumberFormat("es-AR",{style:"currency",currency:"ARS",maximumFractionDigits:0}).format(n);function save(){localStorage.setItem("rl-config",JSON.stringify(config));localStorage.setItem("rl-products",JSON.stringify(products))}function draw(){const logo=el("logo");logo.hidden=!config.logo;if(config.logo)logo.src=config.logo;else logo.removeAttribute("src");el("brand").textContent=config.name;el("tagline").textContent=config.tagline;if(!el("menu"))return;el("menu").replaceChildren();for(const p of products){const box=document.createElement("article");box.className="product";const category=document.createElement("div");category.className="category";category.textContent=p.category;const title=document.createElement("h3");title.textContent=p.name;const price=document.createElement("p");price.className="price";price.textContent=money(p.price);const add=document.createElement("button");add.textContent="Agregar +";add.onclick=()=>{addToCart(p.id,p.name,effectivePrice(p),1);drawCart()};const active=Number(p.offerPrice)>0&&Number(p.offerPrice)<p.price&&(!p.offerUntil||p.offerUntil>=new Date().toISOString().slice(0,10));if(active){const old=document.createElement("del");old.textContent=money(p.price);price.replaceChildren(old,document.createTextNode(" "+money(Number(p.offerPrice))));const offer=document.createElement("span");offer.className="offer";offer.textContent="🔥 OFERTA";box.append(offer)}if(p.image){const pic=document.createElement("img");pic.className="product-photo";pic.src=p.image;pic.alt=p.name;box.append(pic)}box.append(category,title,price,add);el("menu").append(box)}renderPizzaOptions();drawCart()}function effectivePrice(p){return Number(p.offerPrice)>0&&Number(p.offerPrice)<p.price&&(!p.offerUntil||p.offerUntil>=new Date().toISOString().slice(0,10))?Number(p.offerPrice):p.price}function addToCart(key,name,unitPrice,qty){const item=cart[key];if(item){item.qty+=qty}else{cart[key]={name,unitPrice,qty}}}function pizzaProducts(){return products.filter(p=>/pizza/i.test(p.category)&&Number(p.price)>0)}function renderPizzaOptions(){const pizzas=pizzaProducts();const first=el("pizzaFirst"),second=el("pizzaSecond");if(!first||!second)return;const previousFirst=first.value,previousSecond=second.value;first.replaceChildren();second.replaceChildren();for(const p of pizzas){for(const select of [first,second]){const option=document.createElement("option");option.value=p.id;option.textContent=p.name+" ("+money(effectivePrice(p))+")";select.append(option)}}if(pizzas.some(p=>p.id===previousFirst))first.value=previousFirst;if(pizzas.some(p=>p.id===previousSecond))second.value=previousSecond;if(el("pizzaBuilder")){el("pizzaBuilder").hidden=!pizzas.length;updatePizzaBuilder()}}function pizzaSelection(){const type=el("pizzaType").value,a=products.find(p=>p.id===el("pizzaFirst").value),b=products.find(p=>p.id===el("pizzaSecond").value);if(!a)return null;const name=type==="half"?"Media "+a.name:type==="mixed"&&b?"Pizza mitad "+a.name+" / mitad "+b.name:a.name;const price=type==="half"?effectivePrice(a)/2:type==="mixed"&&b?(effectivePrice(a)+effectivePrice(b))/2:effectivePrice(a);const key="pizza:"+type+":"+a.id+(type==="mixed"?":"+b?.id:"");return {key,name,price}}function updatePizzaBuilder(){const mixed=el("pizzaType").value==="mixed";const row=el("pizzaSecondRow");row.hidden=!mixed;row.style.display=mixed?"block":"none";el("pizzaSecond").disabled=!mixed;const selection=pizzaSelection();el("pizzaPrice").textContent=selection?"Precio por unidad: "+money(selection.price):""}for(const id of ["pizzaType","pizzaFirst","pizzaSecond"])if(el(id))el(id).onchange=updatePizzaBuilder;if(el("addPizza"))el("addPizza").onclick=()=>{const selection=pizzaSelection(),qty=Number(el("pizzaQty").value);if(!selection||!Number.isInteger(qty)||qty<1||qty>30){el("pizzaNotice").textContent="Elegí una pizza y una cantidad entre 1 y 30.";return}addToCart(selection.key,selection.name,selection.price,qty);el("pizzaNotice").textContent="Agregado: "+qty+" × "+selection.name;drawCart()};function currentSubtotal(){return Object.values(cart).reduce((sum,item)=>sum+item.unitPrice*item.qty,0)}function updateChange(){const cash=el("checkout").elements.namedItem("cashAmount").value;const due=currentSubtotal()+(el("mode").value==="delivery"?(Number(config.delivery)||0):0);el("change").textContent=el("payment").value==="efectivo"&&cash!==""&&Number(cash)>=due?"Cambio estimado: "+money(Number(cash)-due):""}function drawCart(){const target=el("cart");if(!target)return;target.replaceChildren();for(const [key,item] of Object.entries(cart)){if(item.qty<1)continue;const row=document.createElement("div");row.className="line";const label=document.createElement("span");label.textContent=item.qty+" × "+item.name+" — "+money(item.unitPrice*item.qty);const controls=document.createElement("span");controls.className="cart-controls";const plus=document.createElement("button");plus.type="button";plus.textContent="+";plus.setAttribute("aria-label","Agregar uno de "+item.name);plus.onclick=()=>{item.qty++;drawCart()};const minus=document.createElement("button");minus.type="button";minus.textContent="−";minus.setAttribute("aria-label","Quitar uno de "+item.name);minus.onclick=()=>{item.qty--;if(item.qty<1)delete cart[key];drawCart()};controls.append(minus,plus);row.append(label,controls);target.append(row)}if(!Object.keys(cart).length)target.textContent="Tu carrito está vacío.";const delivery=el("mode")?.value==="delivery"?Number(config.delivery)||0:0;el("total").textContent="Total: "+money(currentSubtotal()+delivery);if(el("send"))el("send").disabled=!currentSubtotal();if(el("checkout"))updateChange()}if(el("payment"))el("payment").onchange=()=>{el("cashRow").hidden=el("payment").value!=="efectivo";updateChange()};if(el("checkout"))el("checkout").elements.namedItem("cashAmount").oninput=updateChange;if(el("mode"))el("mode").onchange=()=>{el("addressRow").hidden=el("mode").value!=="delivery";el("addressRow").querySelector("input").required=!el("addressRow").hidden;drawCart()};function fillSettings(){if(!el("settingsForm"))return;for(const [k,v] of Object.entries(config)){const input=el("settingsForm").elements.namedItem(k);if(input)input.value=v}}async function fileToData(file){if(!file)return "";if(!["image/jpeg","image/png","image/webp"].includes(file.type)||file.size>1048576)throw new Error("Elegí una imagen JPG, PNG o WebP de hasta 1 MB.");return await new Promise((resolve,reject)=>{const reader=new FileReader();reader.onload=()=>resolve(reader.result);reader.onerror=()=>reject(new Error("No se pudo leer la imagen"));reader.readAsDataURL(file)})}if(el("removeLogo"))el("removeLogo").onclick=()=>{config.logo="";try{save();draw()}catch{alert("No se pudo guardar el cambio")}};if(el("settingsForm"))el("settingsForm").onsubmit=async e=>{e.preventDefault();const d=new FormData(e.currentTarget);const phone=String(d.get("phone")).replace(/\D/g,"");if(!/^\d{10,16}$/.test(phone))return alert("Ingresá un número de WhatsApp válido con código de país.");try{const logoFile=e.currentTarget.elements.namedItem("logoFile").files[0];const logo=logoFile?await fileToData(logoFile):config.logo;const next={name:String(d.get("name")).trim(),tagline:String(d.get("tagline")).trim(),phone,delivery:Math.max(0,Number(d.get("delivery"))||0),logo};const prev=config;config=next;try{save()}catch(err){config=prev;throw err}draw();alert("Configuración guardada en este dispositivo.")}catch(err){alert(err.message||"No se pudo guardar. Probá con una imagen más pequeña.")}};if(el("productForm"))el("productForm").onsubmit=async e=>{e.preventDefault();const d=new FormData(e.currentTarget),price=Number(d.get("price"));if(!Number.isFinite(price)||price<=0)return;try{const image=await fileToData(e.currentTarget.elements.namedItem("imageFile").files[0]);const offerPrice=d.get("offerPrice")?Number(d.get("offerPrice")):null;const offerUntil=String(d.get("offerUntil")||"");if(offerPrice!==null&&(!Number.isFinite(offerPrice)||offerPrice<=0||offerPrice>=price))throw new Error("El precio de oferta debe ser menor que el precio normal.");const product={id:crypto.randomUUID(),name:String(d.get("name")).trim(),category:String(d.get("category")).trim(),price,image,offerPrice,offerUntil};products.push(product);try{save()}catch(err){products.pop();throw err}draw();e.currentTarget.reset()}catch(err){alert(err.message||"No se pudo guardar el producto. Probá con una imagen más pequeña.")}};if(el("reset"))el("reset").onclick=()=>{if(confirm("¿Restaurar el menú de demostración?")){products=structuredClone(initial);cart={};save();draw()}};let pendingWhatsappUrl="";let remoteOrderEndpoint=null;let remoteBusy=false;if(el("cancelConfirmation"))el("cancelConfirmation").onclick=()=>el("confirmation").close();if(el("confirmWhatsapp"))el("confirmWhatsapp").onclick=()=>{if(!pendingWhatsappUrl)return;const url=pendingWhatsappUrl;pendingWhatsappUrl="";el("confirmation").close();const opened=window.open(url,"_blank","noopener,noreferrer");if(!opened)window.location.href=url;el("feedback").textContent="Revisá WhatsApp y presioná Enviar. El pedido no se guarda todavía en un servidor."};if(el("checkout"))el("checkout").onsubmit=async e=>{e.preventDefault();el("feedback").textContent="";if(remoteOrderEndpoint){await sendRemoteOrder(e.currentTarget);return;}const items=Object.values(cart).filter(item=>item.qty>0);if(!items.length)return;const d=new FormData(e.currentTarget);if(!/^\d{10,16}$/.test(config.phone)){el("feedback").textContent="Primero configurá el WhatsApp del comercio.";el("feedback").textContent="Este comercio aún no configuró su WhatsApp. No se puede enviar el pedido.";return}const mode=String(d.get("mode")),address=String(d.get("address")||"").trim();if(mode==="delivery"&&!address){el("feedback").textContent="Indicá la dirección de entrega.";return}const payment=String(d.get("payment"));const cashRaw=String(d.get("cashAmount")||"").trim();const subtotal=currentSubtotal(),delivery=mode==="delivery"?(Number(config.delivery)||0):0,total=subtotal+delivery;let paymentText=payment==="transferencia"?"Transferencia":"Efectivo";if(payment==="efectivo"&&cashRaw){const cash=Number(cashRaw);if(!Number.isFinite(cash)||cash<total){el("feedback").textContent="El importe en efectivo debe alcanzar para cubrir el total.";return}paymentText+=" (paga con "+money(cash)+", cambio "+money(cash-total)+")"}const lines=items.map(item=>"• "+item.qty+" × "+item.name+": "+money(item.qty*item.unitPrice));const msg=["Hola, quiero hacer un pedido a "+config.name+":",...lines,"Subtotal: "+money(subtotal),"Delivery: "+money(delivery),"TOTAL: "+money(total),"Cliente: "+String(d.get("customer")).trim(),"Entrega: "+(mode==="delivery"?"Delivery":"Retiro en local"),mode==="delivery"?"Dirección: "+address:"","Pago: "+paymentText,"Observaciones: "+(String(d.get("notes")||"").trim()||"Sin observaciones")].filter(Boolean).join("\n");pendingWhatsappUrl="https://wa.me/"+config.phone+"?text="+encodeURIComponent(msg);el("orderSummary").textContent=msg;el("confirmation").showModal()};fillSettings();draw();

// Modo remoto opt-in: ?api=https://api.ejemplo.com&comercio=slug
// No modifica la demo local por defecto. No habilita registro de pedidos en servidor.
async function cargarCatalogoRemoto() {
  if (!el("menu")) return;
  const params = new URLSearchParams(location.search);
  const api = params.get("api"), slug = params.get("comercio");
  if (!api && !slug) return;
  const aviso = document.createElement("p");
  aviso.setAttribute("role", "status");
  aviso.className = "hint";
  el("menu").before(aviso);
  if (!api || !slug || !/^[a-z0-9-]{1,160}$/.test(slug)) {
    aviso.textContent = "Para conectar un comercio indicá api y comercio válidos en la dirección.";
    el("menu").replaceChildren();
    el("send").disabled = true;
    return;
  }
  let endpoint;
  try {
    const url = new URL(api);
    if (url.protocol !== "https:" && !(url.hostname === "localhost" || url.hostname === "127.0.0.1")) throw Error("API insegura");
    endpoint = new URL(url.pathname.replace(/\/$/, "") + "/api/comercios/" + encodeURIComponent(slug) + "/catalogo", url.origin);
  } catch {
    aviso.textContent = "La URL de la API debe utilizar HTTPS (o localhost para pruebas).";
    el("menu").replaceChildren();
    el("send").disabled = true;
    return;
  }
  remoteOrderEndpoint = null;
  aviso.textContent = "Cargando menú del comercio…";
  el("menu").replaceChildren();
  el("send").disabled = true;
  try {
    const response = await fetch(endpoint, { headers: {Accept:"application/json"}, cache:"no-store" });
    if (!response.ok) throw Error(response.status === 404 ? "Comercio no disponible o suspendido." : "No se pudo consultar el catálogo.");
    const data = await response.json();
    if (!data?.comercio?.nombre || !Array.isArray(data.productos)) throw Error("Respuesta del servidor inválida.");
    const parsed = data.productos.map(p => ({
      id: String(p.id),
      name: String(p.nombre || ""),
      category: "Productos",
      price: Number(p.precio),
      offerPrice: p.precio_oferta == null ? null : Number(p.precio_oferta),
      offerUntil: p.oferta_hasta ? String(p.oferta_hasta).slice(0, 10) : "",
      image: "", // No cargar rutas de imagen del servidor hasta validar almacenamiento y URLs.
    })).filter(p => p.name && Number.isFinite(p.price) && p.price > 0);
    products = parsed;
    cart = {};
    config = {
      ...defaults,
      name: String(data.comercio.nombre),
      phone: String(data.comercio.telefono_whatsapp || "").replace(/\\D/g, ""),
    };
    remoteOrderEndpoint = new URL(endpoint.href.replace(/\/catalogo$/, "/pedidos"));
    draw();
    aviso.textContent = "Menú conectado. Al confirmar, el pedido se registrará en el servidor; el precio final será el que confirme el comercio.";
  } catch (error) {
    remoteOrderEndpoint = null;
    products = [];
    cart = {};
    draw();
    aviso.textContent = error.message || "No se pudo cargar el catálogo. Intentá nuevamente.";
  }
}
async function sendRemoteOrder(form) {
  if(remoteBusy || !remoteOrderEndpoint) return;
  const items = Object.entries(cart).filter(([,v])=>v.qty>0);
  if(!items.length) return;
  const orderItems = [];
  for(const [key,item] of items) {
    if(!/^\d+$/.test(key) || !Number.isInteger(item.qty) || item.qty<1 || item.qty>30) {
      el("feedback").textContent = "Hay un producto no compatible con el pedido online. Vacía el carrito y volvé a intentarlo.";
      return;
    }
    orderItems.push({producto_id:Number(key),cantidad:item.qty});
  }
  const d = new FormData(form);
  const mode = String(d.get("mode"));
  const address = String(d.get("address")||"").trim();
  if(mode==="delivery"&&!address){el("feedback").textContent="Ingresá la dirección de entrega.";return;}
  const body = {cliente_nombre:String(d.get("customer")||"").trim(),modalidad:mode,direccion:address,pago:String(d.get("payment")),notas:String(d.get("notes")||""),items:orderItems};
  if(!body.cliente_nombre){el("feedback").textContent="Ingresá tu nombre.";return;}
  remoteBusy=true;el("send").disabled=true;
  el("feedback").textContent="Registrando pedido… No vuelvas a enviarlo.";
  try {
    const response = await fetch(remoteOrderEndpoint,{method:"POST",headers:{"Content-Type":"application/json","Accept":"application/json"},body:JSON.stringify(body)});
    if(!response.ok) throw Error(response.status===422?"El pedido contiene productos no disponibles. Actualizá la página.":"No se pudo confirmar el pedido. Verificá con el comercio antes de reintentarlo para evitar duplicados.");
    const result = await response.json();
    if(!Number.isInteger(Number(result.id)) || !Number.isFinite(Number(result.total))) throw Error("El servidor respondió sin datos suficientes. Consultá al comercio antes de reintentar.");
    const summary = "Pedido #"+result.id+" registrado · Total confirmado: "+money(Number(result.total))+".";
    cart={};drawCart();
    el("feedback").textContent=summary+" El comercio podrá verlo en su panel.";
    form.reset();
    el("addressRow").hidden=true;
    el("cashRow").hidden=false;
    if(/^\d{10,16}$/.test(config.phone)) {
      const link=document.createElement("a");
      link.href="https://wa.me/"+config.phone+"?text="+encodeURIComponent("Hola, registré el pedido #"+result.id+" en "+config.name+". Total: "+money(Number(result.total)));
      link.target="_blank";link.rel="noopener noreferrer";link.textContent="Avisar por WhatsApp (opcional)";
      el("feedback").append(document.createTextNode(" "),link);
    }
  } catch(error) {
    el("feedback").textContent=error.message||"No se pudo confirmar. Consultá al comercio antes de reintentar.";
  } finally {remoteBusy=false;drawCart();}
}
cargarCatalogoRemoto();
