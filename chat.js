const msgs = document.getElementById('chatMessages');
const input = document.getElementById('messageInput');
const btn = document.getElementById('sendButton');
const micBtn = document.getElementById('micButton');
const typing = document.getElementById('typingIndicator');
const status = document.getElementById('statusText');

let processing = false;
let lastTime = 0;
let isRecording = false;
let mediaRecorder;
let audioChunks = [];

// Función para habilitar/deshabilitar controles
function toggleControls(disabled = false) {
    input.disabled = disabled;
    btn.disabled = disabled;
    processing = disabled;
    
    if (!disabled) {
        input.focus();
    }
}

// Función para mostrar/ocultar indicador de escritura
function toggleTyping(show = false) {
    if (show) {
        typing.classList.remove('d-none');
        if (status) status.textContent = 'Escribiendo...';
    } else {
        typing.classList.add('d-none');
        if (status) status.textContent = 'Asistente IA';
    }
}

function addMsg(msg, user = false) {
    const div = document.createElement('div');
    div.className = `mb-3 message-container ${user ? 'text-end' : ''}`;
    div.innerHTML = `<div class="message-${user ? 'user' : 'bot'}"><div>${msg}</div><small class="text-muted">${new Date().toLocaleTimeString('es-ES', {hour:'2-digit',minute:'2-digit'})}</small></div>`;
    
    // Agregar con un pequeño delay para activar la animación
    msgs.appendChild(div);
    
    // Scroll suave hacia abajo
    setTimeout(() => {
        msgs.scrollTop = msgs.scrollHeight;
    }, 100);
}

async function send() {
    const msg = input.value.trim();
    if (!msg || processing) return;
    
    const now = Date.now();
    if (now - lastTime < 2000) {
        addMsg('⏱️ Espera 2 segundos entre mensajes');
        return;
    }
    lastTime = now;
    
    // Agregar mensaje del usuario
    addMsg(msg, true);
    input.value = '';
    
    // Deshabilitar controles y mostrar indicador
    toggleControls(true);
    toggleTyping(true);
    
    try {
        const form = new FormData();
        form.append('message', msg);
        
        const res = await fetch('chat.php', {
            method: 'POST', 
            body: form, 
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        });
        
        if (!res.ok) {
            throw new Error(`Error HTTP: ${res.status}`);
        }
        
        const data = await res.json();
        
        if (data.success) {
            addMsg(data.message);
        } else {
            addMsg(`❌ Error: ${data.message}`);
        }
        
    } catch (e) {
        console.error('Error en la solicitud:', e);
        addMsg('❌ Error de conexión. Verifica tu conexión a internet y que el servidor esté funcionando.');
    } finally {
        // Siempre habilitar controles al final
        toggleControls(false);
        toggleTyping(false);
    }
}

// Función para inicializar el chat
function initChat() {
    // Asegurar que los controles estén habilitados
    toggleControls(false);
    toggleTyping(false);
    
    // Enfocar el input
    setTimeout(() => {
        input.focus();
    }, 100);
}

// Función para iniciar/detener grabación
async function toggleRecording() {
    if (isRecording) {
        stopRecording();
    } else {
        await startRecording();
    }
}

async function startRecording() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorder = new MediaRecorder(stream);
        audioChunks = [];
        
        mediaRecorder.ondataavailable = event => {
            audioChunks.push(event.data);
        };
        
        mediaRecorder.onstop = async () => {
            const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
            await convertSpeechToText(audioBlob);
            
            // Detener el stream
            stream.getTracks().forEach(track => track.stop());
        };
        
        mediaRecorder.start();
        isRecording = true;
        micBtn.classList.add('recording');
        micBtn.querySelector('i').className = 'fas fa-stop';
        
        if (status) status.textContent = 'Grabando...';
        
    } catch (error) {
        console.error('Error al acceder al micrófono:', error);
        addMsg('❌ Error: No se pudo acceder al micrófono. Verifica los permisos.');
    }
}

function stopRecording() {
    if (mediaRecorder && mediaRecorder.state !== 'inactive') {
        mediaRecorder.stop();
    }
    
    isRecording = false;
    micBtn.classList.remove('recording');
    micBtn.querySelector('i').className = 'fas fa-microphone';
    
    if (status) status.textContent = 'Procesando audio...';
}

async function convertSpeechToText(audioBlob) {
    try {
        // Usar Web Speech API si está disponible
        if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
            const recognition = new SpeechRecognition();
            
            recognition.lang = 'es-ES';
            recognition.continuous = false;
            recognition.interimResults = false;
            
            recognition.onresult = (event) => {
                const transcript = event.results[0][0].transcript;
                input.value = transcript;
                if (status) status.textContent = 'Asistente IA';
                
                // Enviar automáticamente el texto convertido
                setTimeout(() => {
                    send();
                }, 500);
            };
            
            recognition.onerror = (event) => {
                console.error('Error en reconocimiento de voz:', event.error);
                addMsg('❌ Error al convertir audio a texto. Intenta de nuevo.');
                if (status) status.textContent = 'Asistente IA';
            };
            
            recognition.onend = () => {
                if (status) status.textContent = 'Asistente IA';
            };
            
            // Crear un audio temporal para reproducir y que el recognition lo capture
            const audio = new Audio(URL.createObjectURL(audioBlob));
            recognition.start();
            
        } else {
            addMsg('❌ Tu navegador no soporta reconocimiento de voz. Usa Chrome o Edge.');
            if (status) status.textContent = 'Asistente IA';
        }
        
    } catch (error) {
        console.error('Error al convertir audio:', error);
        addMsg('❌ Error al procesar el audio.');
        if (status) status.textContent = 'Asistente IA';
    }
}

// Event listeners
btn.onclick = send;
micBtn.onclick = toggleRecording;
input.onkeypress = e => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        send();
    }
};

// Inicializar cuando la página esté lista
document.addEventListener('DOMContentLoaded', initChat);

// Verificar periódicamente que los controles estén habilitados
setInterval(() => {
    if (!processing && (input.disabled || btn.disabled)) {
        console.log('Restaurando controles...');
        toggleControls(false);
    }
}, 5000);

// Función para copiar código al portapapeles
function copyCode(button) {
    const codeBlock = button.closest('.code-block');
    const codeElement = codeBlock.querySelector('code');
    const text = codeElement.textContent;
    
    // Usar la API moderna del portapapeles si está disponible
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            // Cambiar el texto del botón temporalmente
            const originalText = button.textContent;
            button.textContent = '✅ Copiado';
            button.style.backgroundColor = '#2ea043';
            
            setTimeout(() => {
                button.textContent = originalText;
                button.style.backgroundColor = '#238636';
            }, 2000);
        }).catch(err => {
            console.error('Error al copiar:', err);
            fallbackCopyText(text, button);
        });
    } else {
        // Fallback para navegadores más antiguos
        fallbackCopyText(text, button);
    }
}

// Función fallback para copiar texto
function fallbackCopyText(text, button) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        const originalText = button.textContent;
        button.textContent = '✅ Copiado';
        button.style.backgroundColor = '#2ea043';
        
        setTimeout(() => {
            button.textContent = originalText;
            button.style.backgroundColor = '#238636';
        }, 2000);
    } catch (err) {
        console.error('Error al copiar:', err);
        button.textContent = '❌ Error';
        setTimeout(() => {
            button.textContent = '📋 Copiar';
        }, 2000);
    } finally {
        document.body.removeChild(textArea);
    }
}
