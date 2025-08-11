<?php
// Archivo deprecado: el contenido del formulario ahora vive en public/index.php
// Se mantiene vacío para evitar rutas rotas antiguas.
http_response_code(410); // Gone
header('Content-Type: text/plain; charset=utf-8');
echo 'Este formulario ha sido movido. Usa /app-php/public/';
