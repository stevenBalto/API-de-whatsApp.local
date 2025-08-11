# WhatsAPI Local (Resumen)

Comandos mínimos para levantar la API de WhatsApp (whatsapp-web.js + Express).

## Primera vez
```powershell
cd C:\xampp\htdocs\whatsapi-local\bridge-node
copy .env.example .env
npm install
```

## Iniciar API (QR en http://localhost:3001)
```powershell
cd C:\xampp\htdocs\whatsapi-local\bridge-node
npm start
```

## Detener
CTRL + C en la ventana.

## Reiniciar luego
```powershell
cd C:\xampp\htdocs\whatsapi-local\bridge-node
npm start
```

## Forzar cierre de procesos Node (si puerto ocupado)
```powershell
taskkill /F /IM node.exe
```

## Reset de sesión (nuevo QR)
```powershell
curl -X POST http://localhost:3001/reset-session
```

## Enviar mensaje (ejemplo)
```powershell
curl -X POST http://localhost:3001/send `
  -H "Content-Type: application/json" `
  -d '{"to":"5215555555555","message":"Hola"}'
```

Para detalles completos revisa `bridge-node/README.md`.
