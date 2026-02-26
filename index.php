<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>AgroSentry IA - CEO Juan Manuel Yandar</title>
    
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#2e7d32">
    
    <style>
        :root {
            --verde-neon: #39FF14; --rojo-neon: #FF3131; --azul-neon: #00FFFF; --amarillo-neon: #FFF01F;
            --morado-neon: #BC13FE; --oscuro: #121212; --gris-card: #1e1e1e;
        }
        body { font-family: 'Segoe UI', sans-serif; background: var(--oscuro); margin: 0; padding: 20px; color: #eee; }

        /* --- PANTALLA DE SEGURIDAD --- */
        #pantalla-login {
            position: fixed; top:0; left:0; width: 100%; height: 100%; 
            background: #000; display: flex; justify-content: center; align-items: center; z-index: 9999;
        }
        .login-card {
            background: var(--gris-card); padding: 40px; border-radius: 20px;
            border: 2px solid var(--azul-neon); text-align: center; width: 320px;
        }
        .login-card input {
            width: 100%; padding: 12px; margin: 10px 0; border-radius: 5px;
            border: 1px solid #444; background: #222; color: white; text-align: center; box-sizing: border-box;
        }
        #sistema-principal { display: none; }

        .container { max-width: 1400px; margin: auto; display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 15px; }
        
        header { background: #000; color: var(--verde-neon); padding: 15px; border-radius: 15px; text-align: center; margin-bottom: 20px; border: 2px solid var(--verde-neon); box-shadow: 0 0 15px var(--verde-neon); grid-column: 1 / -1; }
        .card { background: var(--gris-card); border-radius: 12px; padding: 20px; box-shadow: 0 8px 15px rgba(0,0,0,0.5); border: 1px solid #333; }

        #notificador { position: fixed; top: 15px; right: 15px; z-index: 1000; width: 350px; }
        .alerta { padding: 18px; margin-bottom: 12px; border-radius: 10px; color: #000; font-weight: 900; font-size: 1rem; animation: slideIn 0.4s ease; box-shadow: 0 0 20px rgba(255,255,255,0.2); text-transform: uppercase; }
        
        .alerta-riego { background: var(--azul-neon); box-shadow: 0 0 15px var(--azul-neon); }
        .alerta-siembra { background: var(--verde-neon); box-shadow: 0 0 15px var(--verde-neon); }
        .alerta-cosecha { background: var(--amarillo-neon); box-shadow: 0 0 15px var(--amarillo-neon); }
        .alerta-quimico { background: var(--morado-neon); color: white; box-shadow: 0 0 15px var(--morado-neon); }
        .alerta-perdida { background: var(--rojo-neon); color: white; box-shadow: 0 0 15px var(--rojo-neon); }

        .resumen-financiero { grid-column: 1 / -1; display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 10px; background: #000; padding: 20px; border-radius: 15px; border: 2px solid var(--verde-neon); text-align: center; }
        .monto { font-size: 1.6rem; font-weight: bold; margin: 5px 0; }

        .registro-maestro { grid-column: 1 / -1; background: #000; color: var(--verde-neon); padding: 20px; border-radius: 10px; font-family: monospace; height: 200px; overflow-y: auto; border: 1px solid #444; }
        
        .btn { padding: 12px; border: none; border-radius: 5px; color: #000; font-weight: bold; cursor: pointer; width: 100%; margin: 5px 0; text-transform: uppercase; }
        .progress-bar { background: #333; height: 25px; border-radius: 10px; overflow: hidden; margin: 10px 0; border: 1px solid #555; }
        .progress-fill { background: var(--azul-neon); height: 100%; width: 0%; transition: 0.8s; box-shadow: 0 0 10px var(--azul-neon); }
        
        input[type="password"] { width: 90%; padding: 10px; text-align: center; background: #333; color: white; border: 1px solid var(--verde-neon); margin-bottom: 10px; }

        @keyframes slideIn { from { transform: translateX(100%); } to { transform: translateX(0); } }
    </style>
</head>
<body>

<div id="pantalla-login">
    <div class="login-card">
        <h2 style="color: var(--azul-neon);">🔒 ACCESO RESTRINGIDO</h2>
        <input type="text" id="nombre-empleado" placeholder="Nombre del Trabajador">
        <input type="password" id="pass-sistema" placeholder="Contraseña">
        <button class="btn" style="background: var(--azul-neon);" onclick="loginManual()">Entrar al Sistema</button>
        <p style="font-size: 0.7rem; color: #666; margin: 15px 0;">O escanee su tarjeta</p>
        <button class="btn" style="background: var(--morado-neon); color: white;" onclick="loginRFID()">RFID Trabajador</button>
    </div>
</div>

<div id="sistema-principal">
    <div id="notificador"></div>

    <header>
        <h1>🛰️ AGROSENTRY IA - PANEL MAESTRO AUTÓNOMO</h1>
        <p style="margin: 5px 0;"><b>Desarrollador CEO: Juan Manuel Yandar</b></p>
        <p id="txt-bienvenida">Detección Automática de Humedad | Seguridad 2319</p>
    </header>

    <div class="container">
        <section class="resumen-financiero">
            <div><p>🟢 VENTAS</p><div id="t-ventas" class="monto" style="color:var(--verde-neon)">$0</div></div>
            <div><p>🔴 GASTOS</p><div id="t-gastos" class="monto" style="color:var(--rojo-neon)">$0</div></div>
            <div><p>🧪 INSUMOS</p><div id="t-insumos" class="monto" style="color:var(--morado-neon)">$0</div></div>
            <div><p>📉 PÉRDIDAS</p><div id="t-perdidas" class="monto" style="color:var(--amarillo-neon)">$0</div></div>
            <div><p>💰 NETO</p><div id="t-neto" class="monto" style="color:#fff">$0</div></div>
        </section>

        <section class="card">
            <h2 style="color:var(--azul-neon)">💧 Humedad de Tierra</h2>
            <p>Estado del Suelo: <b id="hum-val">0%</b></p>
            <div class="progress-bar"><div id="hum-fill" class="progress-fill"></div></div>
            <p id="riego-status" style="color:var(--amarillo-neon)">SENSOR: Analizando...</p>
            <div style="display:flex; gap:5px;">
                <button class="btn" style="background:var(--azul-neon)" onclick="riegoManual(true)">Forzar Riego</button>
                <button class="btn" style="background:var(--rojo-neon); color:white;" onclick="riegoManual(false)">Forzar Parada</button>
            </div>
        </section>

        <section class="card">
            <h2 style="color:var(--verde-neon)">🚜 Tractor IA</h2>
            <div id="tractor-bloqueo">
                <input type="password" id="pin-tractor" placeholder="PIN SEGURIDAD">
                <button class="btn" style="background:var(--verde-neon)" onclick="activarTractor()">Autorizar</button>
            </div>
            <div id="tractor-activo" style="display:none; text-align: center;">
                <p style="color:var(--verde-neon); font-weight:bold;">🚜 COSECHANDO...</p>
                <button class="btn" style="background:var(--rojo-neon); color:white;" onclick="detenerTractor()">Detener y Liquidar</button>
            </div>
        </section>

        <section class="card">
            <h2 style="color:var(--morado-neon)">🏷️ RFID Insumos</h2>
            <button class="btn" style="background:var(--morado-neon); color:white;" onclick="usarRFID('ABONO', '25kg', 180)">RFID Abono</button>
            <button class="btn" style="background:var(--rojo-neon); color:white;" onclick="usarRFID('PESTICIDA', '2L', 120)">RFID Pesticida</button>
            <button class="btn" style="background:#555; color:white;" onclick="usarRFID('FUNGICIDA', '1.5L', 95)">RFID Fungicida</button>
        </section>

        <section class="card">
            <h2 style="color:var(--amarillo-neon)">📦 Ventas Rápidas</h2>
            <button class="btn" style="background:var(--verde-neon)" onclick="vender('Papa', 6000)">🥔 Papa</button>
            <button class="btn" style="background:var(--verde-neon)" onclick="vender('Maíz', 4000)">🌽 Maíz</button>
            <button class="btn" style="background:var(--verde-neon)" onclick="vender('Arveja', 5500)">🌿 Arveja</button>
        </section>

        <section class="registro-maestro" id="log-maestro">
            > AgroSentry IA Iniciado...
        </section>
    </div>
</div>

<script>
    let granja = { hum: 35, riego: false, ventas: 0, gastos: 0, insumos: 0, perdidas: 0, avisos: {}, empleado: "" };
    const PIN = "2319"; 
    const PASS_SISTEMA = "1227";

    function loginManual() {
        const emp = document.getElementById('nombre-empleado').value;
        const pass = document.getElementById('pass-sistema').value;
        if(emp !== "" && pass === PASS_SISTEMA) { entrar(emp); } 
        else { alert("Acceso denegado. Contraseña: 1227"); }
    }

    function loginRFID() { entrar("Trabajador_RFID"); }

    function entrar(nombre) {
        granja.empleado = nombre;
        document.getElementById('pantalla-login').style.display = 'none';
        document.getElementById('sistema-principal').style.display = 'block';
        document.getElementById('txt-bienvenida').innerText = `Operador: ${nombre} | Sistema Autónomo`;
        notificar(`BIENVENIDO: ${nombre}`, "siembra");
    }

    function notificar(msg, tipo) {
        const n = document.getElementById('notificador');
        const div = document.createElement('div');
        div.className = `alerta alerta-${tipo}`;
        div.innerText = msg;
        n.appendChild(div);
        setTimeout(() => div.remove(), 5000);
        const h = new Date().toLocaleTimeString();
        document.getElementById('log-maestro').innerHTML = `<div>[${h}] ${msg}</div>` + document.getElementById('log-maestro').innerHTML;
    }

    setInterval(() => {
        if (granja.hum < 30 && !granja.riego) {
            granja.riego = true;
            notificar("💧 AUTO-CORRECCIÓN: Iniciando riego automático.", "riego");
        }
        if (granja.riego) {
            granja.hum += 2;
            document.getElementById('riego-status').innerText = "SENSOR: HUMEDECIÉNDOSE...";
            if (granja.hum >= 80) {
                granja.hum = 80; granja.riego = false;
                notificar("✅ AUTO-CORRECCIÓN: 80% alcanzado. Apagado.", "riego");
            }
        } else {
            document.getElementById('riego-status').innerText = "SENSOR: TIERRA OK";
            if (granja.hum > 10) granja.hum -= 0.2;
        }
        actualizarUI();
    }, 2000);

    function activarTractor() {
        if(document.getElementById('pin-tractor').value === PIN) {
            document.getElementById('tractor-bloqueo').style.display = 'none';
            document.getElementById('tractor-activo').style.display = 'block';
            notificar(`🚜 TRACTOR: Autorizado por ${granja.empleado}`, "siembra");
            granja.gastos += 150; actualizarFinanzas();
        } else { alert("PIN TRACTOR: 2319"); }
    }

    function detenerTractor() {
        let bruto = 10000; let merma = Math.floor(bruto * 0.12);
        granja.ventas += (bruto - merma); granja.perdidas += merma;
        notificar(`🛑 COSECHA: Pérdida -$${merma}`, "perdida");
        document.getElementById('tractor-bloqueo').style.display = 'block';
        document.getElementById('tractor-activo').style.display = 'none';
        document.getElementById('pin-tractor').value = "";
        actualizarFinanzas();
    }

    function usarRFID(tipo, dosis, costo) {
        granja.insumos += costo;
        notificar(`🧪 RFID: ${tipo} (${dosis}) aplicado por ${granja.empleado}.`, "quimico");
        actualizarFinanzas();
    }

    function vender(p, v) {
        granja.ventas += v;
        notificar(`💰 VENTA: ${p} (+$${v})`, "cosecha");
        actualizarFinanzas();
    }

    function actualizarUI() {
        document.getElementById('hum-val').innerText = Math.floor(granja.hum) + "%";
        document.getElementById('hum-fill').style.width = granja.hum + "%";
    }

    function actualizarFinanzas() {
        document.getElementById('t-ventas').innerText = "$" + granja.ventas.toLocaleString();
        document.getElementById('t-gastos').innerText = "$" + granja.gastos.toLocaleString();
        document.getElementById('t-insumos').innerText = "$" + granja.insumos.toLocaleString();
        document.getElementById('t-perdidas').innerText = "$" + granja.perdidas.toLocaleString();
        document.getElementById('t-neto').innerText = "$" + (granja.ventas - granja.gastos - granja.insumos).toLocaleString();
    }
</script>

<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('./sw.js')
                .then(reg => console.log('AgroSentry PWA: Activa'))
                .catch(err => console.log('Error de PWA:', err));
        });
    }
</script>

</body>
</html>