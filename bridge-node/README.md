# WhatsAPI Local Bridge

Puente/API local para enviar mensajes de WhatsApp usando **[whatsapp-web.js](https://github.com/pedroslopez/whatsapp-web.js)** sobre **Node.js (Express)** y una interfaz PHP opcional.

> Objetivo: tener un servicio sencillo que persista la sesión del número de WhatsApp y permita enviar mensajes vía HTTP o formulario, evitando re‑escanear el QR tras cada reinicio.

## 🗂 Estructura principal
```
whatsapi-local/
 ├─ bridge-node/         # Servidor Node (API WhatsApp)
 │   ├─ server.js
 │   ├─ .env.example
 │   └─ (carpetas de sesión: session/, .wwebjs_auth/)
 └─ app-php/
		 └─ public/index.php # Interfaz web (opcional) para enviar mensajes
```

## ✨ Características
- Persistencia de sesión (LocalAuth) → no reescaneas mientras conserves la carpeta de sesión.
- Endpoints REST: estado, QR, envío, reinicio, reset y depuración.
- Auto re‑intenta conexión tras desconexión.
- Código minimalista y fácil de extender.
- Interfaz PHP (formulario) que NO genera el QR (solo te indica ir al puerto Node).

## 🚀 Requisitos
- Node.js 18+ (recomendado LTS)
- npm
- (Opcional) PHP + Apache (XAMPP / similar) para la interfaz.

## ⚡ Instalación rápida
```powershell
git clone <TU_REPO>.git
cd whatsapi-local/bridge-node
npm install
copy .env.example .env   # o crea tu .env manualmente
npm start
```
Abre: http://localhost:3001  (verás el QR / estado).

Una vez escaneado el QR (solo una vez) quedará listo para enviar mensajes hasta que borres la carpeta de sesión.

## 🔑 Comandos mínimos (Windows PowerShell)
Primera vez (instalar y crear .env):
```powershell
cd C:\xampp\htdocs\whatsapi-local\bridge-node
copy .env.example .env
npm install
```
Iniciar la API (muestra QR):
```powershell
cd C:\xampp\htdocs\whatsapi-local\bridge-node
npm start
```
Detener:
```powershell
# En la misma ventana donde corre
Ctrl + C
```
Reiniciar luego (ya instalado):
```powershell
cd C:\xampp\htdocs\whatsapi-local\bridge-node
npm start
```
Forzar cierre de procesos Node atascados (opcional):
```powershell
taskkill /F /IM node.exe
```
Reset total de sesión (pedirá nuevo QR):
```powershell
curl -X POST http://localhost:3001/reset-session
```
Nada más es obligatorio para usar la API.

## ❗ Errores comunes & solución
| Problema | Causa probable | Solución |
|----------|----------------|----------|
| No aparece QR | Inicializando todavía / error silencioso | Refresca `/` o revisa `/debug` |
| Se desconecta a veces | Red o cierre de browser headless | El cliente se re‑inicia solo; revisa `/debug` |
| Pide QR cada vez | Carpeta de sesión borrada o distinta ruta | Verifica `WHATS_SESSION_PATH` y permisos |
| 503 al enviar | `ready:false` | Escanea QR primero |
| Puerto en uso | Otro proceso Node | `taskkill /F /IM node.exe` o cambia `PORT` |

## 🔐 Seguridad
No hay autenticación. SIEMPRE protege el puerto si lo expones:
- Reverse proxy (Nginx/Apache) con Basic Auth / IP allowlist.
- Firewall limitando acceso solo a tu LAN o servidor backend.
- No abras el puerto públicamente sin capa adicional.

## 🧩 Extender (ideas rápidas)
- Webhook para mensajes entrantes (`client.on('message', ...)`).
- Cola de envíos y registro en base de datos.
- Rate limiting / API Key.
- Panel web para ver historial.

## ⚖️ Aviso / Disclaimer
Este proyecto no es oficial de WhatsApp. Úsalo bajo tu propia responsabilidad y respetando las políticas y leyes anti‑spam. Evita envíos masivos no solicitados.

## 📄 Licencia
Elige una licencia antes de publicar (MIT recomendada). Crea `LICENSE` y actualiza esta sección.

## 🆘 Soporte rápido
1. Revisa `/debug`.
2. Revisa consola donde corre `node server.js`.
3. Ejecuta `/restart`.
4. Si sigue mal: `/reset-session` y reescanea.

---
¿Mejorar algo? Crea un issue o PR. ¡Disfruta construyendo sobre WhatsAPI Local! 💬
