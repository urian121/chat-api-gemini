# 🔧 Configuración de Google Gemini API

## Pasos para obtener tu API Key

### 1. Accede a Google AI Studio
- Ve a [Google AI Studio](https://makersuite.google.com/app/apikey)
- Inicia sesión con tu cuenta de Google

### 2. Crea una nueva API Key
- Haz clic en "Create API Key"
- Selecciona un proyecto existente o crea uno nuevo
- Copia la API key generada (empieza con "AIza...")

### 3. Configura la API Key en el proyecto
- Abre el archivo `chat.php`
- Busca la línea que dice:
  ```php
  $apiKey = 'tu_api_key_de_gemini_aqui';
  ```
- Reemplázala con tu API key real:
  ```php
  $apiKey = 'AIzaSyC_tu_api_key_real_aqui';
  ```

### 4. Guarda y prueba
- Guarda el archivo `chat.php`
- Abre `index.html` en tu navegador
- Envía un mensaje de prueba

## ⚠️ Importante

- **Nunca compartas** tu API key públicamente
- **No la subas** a repositorios públicos de Git
- **Usa variables de entorno** en producción
- **Configura límites de uso** en Google Cloud Console

## 🔒 Seguridad adicional

Para mayor seguridad en producción, considera:

1. **Usar variables de entorno**:
   ```php
   $apiKey = $_ENV['GEMINI_API_KEY'] ?? 'tu_api_key_aqui';
   ```

2. **Configurar un archivo .env**:
   ```
   GEMINI_API_KEY=tu_api_key_aqui
   ```

3. **Limitar el dominio** en Google Cloud Console

## 📊 Límites de la API

- **Gratuito**: 60 solicitudes por minuto
- **Límite de tokens**: 30,000 tokens por minuto
- **Límite diario**: 1,500 solicitudes por día

Para más información, visita la [documentación oficial de Gemini](https://ai.google.dev/docs). 