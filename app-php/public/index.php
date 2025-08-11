<?php
error_reporting(E_ALL); ini_set('display_errors',1);
require_once __DIR__ . '/../services/WhatsApiService.php';
$config = include __DIR__ . '/../config.php';
if (!is_array($config) || empty($config['whatsapi_base'])) { die('Config inválida'); }
$wa = new WhatsApiService($config['whatsapi_base']);

// Petición AJAX de envío
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json; charset=utf-8');
        $to = trim($_POST['to'] ?? '');
        $message = trim($_POST['message'] ?? '');
        if ($to === '' || $message === '') { http_response_code(400); echo json_encode(['error'=>'Campos requeridos']); exit; }
        try { $r = $wa->send($to,$message); echo json_encode(['ok'=>true,'id'=>$r['id']??null]); }
        catch (Throwable $e) { http_response_code(500); echo json_encode(['error'=>$e->getMessage()]); }
        exit;
}

try { $status = $wa->status(); } catch (Throwable $e) { $status = ['ready'=>false,'qr'=>null,'error'=>$e->getMessage()]; }
$ready = $status['ready'] ?? false; $qr = $status['qr'] ?? null; $err = $status['error'] ?? null;
?><!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8" />
    <title>WhatsAPI Local</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="styles.css?v=<?=time()?>" />
</head>
<body>
    <div class="app">
        <div class="header">
            <div class="brand">WhatsAPI Local</div>
            <div class="status-pill <?= $ready? 'ready':'wait' ?>">
                <?= $ready? 'LISTO ✅':'Esperando QR ⏳' ?>
            </div>
        </div>

            <section class="panel">
                <h2>Estado</h2>
                <?php if($err): ?>
                    <div class="alert err fade-in">Error: <?= htmlspecialchars($err) ?></div>
                <?php endif; ?>
                <div class="qr-box fade-in">
                    <?php if($ready): ?>
                        <div class="alert ok">Conectado a WhatsApp.</div>
                        <small>Sesión activa. No necesitas escanear nada aquí.</small>
                    <?php else: ?>
                        <div class="alert warn" style="background:#332d10;border:1px solid #5a4a10;color:#f5c04a">Aún no conectado.</div>
                        <div>Escanea el código QR sólo en <a href="<?= htmlspecialchars($config['whatsapi_base']) ?>" target="_blank"><?= htmlspecialchars($config['whatsapi_base']) ?></a></div>
                        <small>Esta página ya no muestra el QR para evitar duplicados.</small>
                    <?php endif; ?>
                </div>
            </section>

        <section class="panel">
            <h2>Enviar Mensaje</h2>
            <form class="send" id="sendForm" autocomplete="off">
                <div>
                    <label>Teléfono (sin +, con código de país)</label>
                    <input type="text" name="to" placeholder="5215555555555" required />
                </div>
                <div>
                    <label>Mensaje</label>
                    <textarea name="message" placeholder="Escribe el mensaje" required></textarea>
                </div>
                <div class="input-row">
                    <button type="submit" id="sendBtn" <?= $ready? '':'disabled' ?>>Enviar</button>
                    <small style="align-self:center;">Estado: <span id="liveState" class="code"><?= $ready? 'ready':'waiting' ?></span></small>
                </div>
                <div id="result"></div>
            </form>
        </section>

        <footer>
            &copy; <?= date('Y') ?> WhatsAPI Local · <a href="/status-json" target="_blank">status-json</a>
        </footer>
    </div>

<script>
// Poll ligero para actualizar estado sin recargar siempre el QR (cada 8s)
setInterval(async ()=>{
    try {
        const r = await fetch('<?= htmlspecialchars($config['whatsapi_base']) ?>/status-json');
        const j = await r.json();
        const pill = document.querySelector('.status-pill');
        const live = document.getElementById('liveState');
        if (j.ready) {
            pill.className='status-pill ready'; pill.textContent='LISTO ✅';
            document.getElementById('sendBtn').disabled=false;
            if(live) live.textContent='ready';
        } else {
            if(live) live.textContent='waiting';
        }
    }catch(e){}
},8000);

document.getElementById('sendForm').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const fd = new FormData(e.target);
    const btn = document.getElementById('sendBtn');
    btn.disabled=true; const original=btn.textContent; btn.textContent='Enviando...';
    const resBox = document.getElementById('result'); resBox.innerHTML='';
    try {
        const r = await fetch('', {method:'POST', body:fd});
        const j = await r.json();
        if (j.ok) {
            resBox.innerHTML='<div class="alert ok fade-in">Enviado ID: '+(j.id||'N/A')+'</div>';
            e.target.message.value='';
        } else {
            resBox.innerHTML='<div class="alert err fade-in">'+(j.error||'Error')+'</div>';
        }
    } catch(err) {
        resBox.innerHTML='<div class="alert err fade-in">'+err.message+'</div>';
    } finally {
        btn.disabled=false; btn.textContent=original;
    }
});
</script>
</body>
</html>