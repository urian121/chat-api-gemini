# 🤖 Chat IA - Gemini

Chat moderno con Google Gemini AI, reconocimiento de voz y formateo de código.

## ✨ Características

- **Interfaz moderna** con diseño centrado y responsivo
- **Reconocimiento de voz** - graba audio y convierte a texto
- **Formateo de código** con bloques y botón copiar
- **Animaciones fluidas** en mensajes y botones
- **Integración Gemini** con manejo de errores
- **AJAX puro** sin recargas de página

## 🚀 Instalación

1. Descarga los archivos en tu servidor web
2. Obtén tu API key de [Google AI Studio](https://aistudio.google.com/app/u/1/prompts/new_chat)
3. Edita `chat.php` línea 8:
   ```php
   $apiKey = 'tu_api_key_aqui';
   ```
4. Abre `index.html` en tu navegador

## 🎤 Uso del Micrófono

- Haz clic en el botón rojo del micrófono
- Permite el acceso al micrófono en tu navegador
- Habla tu mensaje y haz clic en stop
- El texto se enviará automáticamente a Gemini

## 📱 Compatibilidad

- **Desktop**: Chrome, Edge, Firefox, Safari
- **Móvil**: Responsive design
- **Voz**: Chrome y Edge (Web Speech API)

## ⚠️ Importante

- Mantén tu API key privada
- Funciona mejor en HTTPS para el micrófono
- Requiere permisos de micrófono del navegador 