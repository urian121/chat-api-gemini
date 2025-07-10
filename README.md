# 🤖 Chat IA - Gemini

Una aplicación de chat moderna y elegante con integración a Google Gemini, diseñada con una interfaz de usuario atractiva y funcional.

## ✨ Características

- **Interfaz limpia** estilo Claude con diseño minimalista
- **Diseño responsivo** que funciona en desktop y móvil
- **AJAX puro** - sin recargas de página
- **Indicador de escritura** animado
- **Mensajes con timestamps**
- **Integración con Google Gemini Pro**
- **Manejo de errores** robusto y detallado
- **Seguridad mejorada** con validación XSS
- **Controles inteligentes** que se deshabilitan durante procesamiento
- **Formateo de texto** con soporte para negritas, cursivas y código
- **Configuraciones de seguridad** integradas de Gemini

## 🚀 Instalación

1. **Clona o descarga** los archivos en tu servidor web
2. **Configura tu API key** de Google Gemini en `chat.php`
3. **Abre** `index.html` en tu navegador

## ⚙️ Configuración

### Configurar API Key de Google Gemini

1. Obtén tu API key de [Google AI Studio](https://makersuite.google.com/app/apikey)
2. Abre el archivo `chat.php`
3. Reemplaza `'tu_api_key_de_gemini_aqui'` con tu API key real:

```php
$apiKey = 'tu-api-key-real-de-gemini-aqui';
```

### Requisitos del servidor

- **PHP 7.4+** con extensión cURL habilitada
- **Servidor web** (Apache, Nginx, etc.)
- **Conexión a internet** para las llamadas a la API de Gemini

## 🎨 Características del diseño

### Colores principales
- **Azul primario**: #0d6efd (mensajes del usuario)
- **Gris claro**: #f8f9fa (mensajes del bot)
- **Bordes suaves**: #e9ecef

### Animaciones
- **Aparición de mensajes** con timestamps
- **Indicador de escritura** con puntos animados
- **Botón de envío** con efectos interactivos
- **Scrollbar personalizada** para mejor experiencia

### Responsive Design
- **Adaptable** a diferentes tamaños de pantalla
- **Optimizado** para móviles y tablets
- **Navegación táctil** amigable

## 🛠️ Estructura de archivos

```
chat-ia-gpt/
├── index.html          # Interfaz principal del chat
├── chat.php           # Backend para comunicación con Gemini
├── chat.js            # Lógica JavaScript del frontend
├── styles.css         # Estilos CSS del chat
└── README.md          # Este archivo
```

## 📱 Uso

1. **Abre** `index.html` en tu navegador
2. **Escribe** tu mensaje en el campo de texto
3. **Presiona Enter** o haz clic en el botón de envío
4. **Espera** la respuesta de Gemini IA

## 🔧 Personalización

### Cambiar el contexto del asistente
En `chat.php`, modifica la línea:
```php
$contextualMessage = "Eres un asistente de IA útil y amigable llamado Gemini...";
```

### Ajustar parámetros de Gemini
En `chat.php`, puedes modificar:
- `"temperature"`: Creatividad de las respuestas (0-1)
- `"topK"`: Diversidad de tokens considerados
- `"topP"`: Probabilidad acumulativa para selección de tokens
- `"maxOutputTokens"`: Longitud máxima de respuesta

### Configurar filtros de seguridad
Gemini incluye filtros de seguridad configurables:
- `HARM_CATEGORY_HARASSMENT`
- `HARM_CATEGORY_HATE_SPEECH`
- `HARM_CATEGORY_SEXUALLY_EXPLICIT`
- `HARM_CATEGORY_DANGEROUS_CONTENT`

## 🚨 Solución de problemas

### "API key no configurada"
- Verifica que hayas reemplazado `'tu_api_key_de_gemini_aqui'` con tu API key real de Google

### "Error de conexión"
- Verifica tu conexión a internet
- Asegúrate de que cURL esté habilitado en PHP

### "Error de API"
- Verifica que tu API key de Gemini sea válida
- Revisa que tengas cuota disponible en Google AI Studio

### La página no carga
- Verifica que PHP esté funcionando en tu servidor
- Revisa los logs de error del servidor
- Consulta el archivo `chat_errors.log` para errores específicos

## 🔒 Seguridad

- **Nunca compartas** tu API key públicamente
- **Usa HTTPS** en producción
- **Implementa límites de uso** para evitar abuso
- **Valida** todas las entradas del usuario
- **Filtros de seguridad** de Gemini activos por defecto

## 📊 Características técnicas

### Validaciones implementadas
- **Sanitización** de entrada con `htmlspecialchars`
- **Límite de caracteres** (4000 máximo)
- **Verificación AJAX** para solicitudes
- **Validación de método** POST únicamente

### Manejo de errores
- **Logging automático** de errores en `chat_errors.log`
- **Respuestas JSON** estructuradas
- **Códigos HTTP** apropiados
- **Timeouts** configurables para cURL

### Formateo de respuestas
- **Conversión automática** de markdown a HTML
- **Soporte para negritas** (`**texto**`)
- **Soporte para cursivas** (`*texto*`)
- **Código inline** con `código`

## 📄 Licencia

Este proyecto es de código abierto. Puedes usarlo, modificarlo y distribuirlo libremente.

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:
1. Haz un fork del proyecto
2. Crea una rama para tu función
3. Envía un pull request

## 📞 Soporte

Si tienes problemas o preguntas:
- Revisa la documentación de [Google Gemini](https://ai.google.dev/docs)
- Verifica los logs de error de tu servidor
- Consulta el archivo `chat_errors.log`
- Asegúrate de que todos los requisitos estén cumplidos

---

¡Disfruta usando tu nuevo chat con Gemini IA! 🎉 