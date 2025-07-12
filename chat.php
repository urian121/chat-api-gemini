<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Configuración de la API de Google Gemini
$apiKey = ''; // Reemplaza con tu API key real
$apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent';

// Función para limpiar y validar la entrada
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Función para validar la API key
function validateApiKey($apiKey) {
    return !empty($apiKey) && $apiKey !== 'tu_api_key_de_gemini_aqui' && strlen($apiKey) > 20;
}

// Función para hacer la solicitud a Gemini
function callGeminiAPI($message, $apiKey, $apiUrl) {
    $data = [
        'contents' => [
            [
                'parts' => [
                    [
                        'text' => $message
                    ]
                ]
            ]
        ],
        'generationConfig' => [
            'temperature' => 0.4,
            'topK' => 25,
            'topP' => 0.85,
            'maxOutputTokens' => 400,
            'candidateCount' => 1,
            'stopSequences' => []
        ],
        'safetySettings' => [
            [
                'category' => 'HARM_CATEGORY_HARASSMENT',
                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
            ],
            [
                'category' => 'HARM_CATEGORY_HATE_SPEECH',
                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
            ],
            [
                'category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT',
                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
            ],
            [
                'category' => 'HARM_CATEGORY_DANGEROUS_CONTENT',
                'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'
            ]
        ]
    ];

    $fullUrl = $apiUrl . '?key=' . $apiKey;
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $fullUrl,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'User-Agent: Chat-IA-Gemini/1.0'
        ],
        CURLOPT_TIMEOUT => 30,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        return ['error' => 'Error de conexión: ' . $error];
    }

    if ($httpCode !== 200) {
        // Intentar obtener más información del error
        $errorInfo = json_decode($response, true);
        $errorMessage = 'Error HTTP: ' . $httpCode;
        
        if (isset($errorInfo['error']['message'])) {
            $errorMessage .= ' - ' . $errorInfo['error']['message'];
        }
        
        return ['error' => $errorMessage];
    }

    return json_decode($response, true);
}

// Función para extraer el texto de la respuesta de Gemini
function extractTextFromResponse($response) {
    if (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
        return $response['candidates'][0]['content']['parts'][0]['text'];
    }
    
    if (isset($response['error'])) {
        return 'Error de API: ' . $response['error']['message'];
    }
    
    return 'Error: No se pudo obtener respuesta de Gemini';
}

// Función para formatear el texto de respuesta
function formatResponse($text) {
    // Primero, formatear bloques de código con triple backticks
    $text = preg_replace_callback('/```(\w+)?\n(.*?)\n```/s', function($matches) {
        $language = isset($matches[1]) && !empty($matches[1]) ? $matches[1] : 'text';
        $code = htmlspecialchars($matches[2], ENT_QUOTES, 'UTF-8');
        return '<div class="code-block"><div class="code-header"><span class="code-language">' . $language . '</span><button class="copy-btn" onclick="copyCode(this)">📋 Copiar</button></div><pre><code class="language-' . $language . '">' . $code . '</code></pre></div>';
    }, $text);
    
    // Formatear bloques de código sin especificar lenguaje
    $text = preg_replace_callback('/```\n(.*?)\n```/s', function($matches) {
        $code = htmlspecialchars($matches[1], ENT_QUOTES, 'UTF-8');
        return '<div class="code-block"><div class="code-header"><span class="code-language">código</span><button class="copy-btn" onclick="copyCode(this)">📋 Copiar</button></div><pre><code>' . $code . '</code></pre></div>';
    }, $text);
    
    // Convertir saltos de línea a HTML (después de procesar bloques de código)
    $text = nl2br($text);
    
    // Formatear texto en negrita
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
    
    // Formatear texto en cursiva (evitar conflicto con negritas)
    $text = preg_replace('/(?<!\*)\*([^*]+)\*(?!\*)/', '<em>$1</em>', $text);
    
    // Formatear código inline (evitar conflicto con bloques de código)
    $text = preg_replace('/(?<!`)`([^`]+)`(?!`)/', '<code class="inline-code">$1</code>', $text);
    
    // Formatear listas con viñetas
    $text = preg_replace('/^\s*[-*+]\s+(.+)$/m', '<li>$1</li>', $text);
    $text = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $text);
    
    // Formatear listas numeradas
    $text = preg_replace('/^\s*(\d+)\.\s+(.+)$/m', '<li>$2</li>', $text);
    $text = preg_replace('/(<li>.*<\/li>)/s', '<ol>$1</ol>', $text);
    
    // Formatear títulos
    $text = preg_replace('/^### (.+)$/m', '<h3>$1</h3>', $text);
    $text = preg_replace('/^## (.+)$/m', '<h2>$1</h2>', $text);
    $text = preg_replace('/^# (.+)$/m', '<h1>$1</h1>', $text);
    
    return $text;
}

// Función para registrar errores
function logError($message) {
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message" . PHP_EOL;
    error_log($logMessage, 3, 'chat_errors.log');
}

// Verificar que sea una solicitud POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Verificar que sea una solicitud AJAX
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
    exit;
}

try {
    // Obtener y validar el mensaje
    $message = isset($_POST['message']) ? sanitizeInput($_POST['message']) : '';
    
    if (empty($message)) {
        echo json_encode(['success' => false, 'message' => 'El mensaje no puede estar vacío']);
        exit;
    }
    
    if (strlen($message) > 4000) {
        echo json_encode(['success' => false, 'message' => 'El mensaje es demasiado largo (máximo 4000 caracteres)']);
        exit;
    }
    
    // Validar API key
    if (!validateApiKey($apiKey)) {
        echo json_encode(['success' => false, 'message' => 'API key no configurada. Por favor, configura tu API key de Google Gemini en chat.php']);
        exit;
    }
    
    // Verificar si cURL está disponible
    if (!function_exists('curl_init')) {
        echo json_encode(['success' => false, 'message' => 'cURL no está disponible en este servidor']);
        exit;
    }

    // Preparar el contexto para Gemini
    $contextualMessage = "Eres Gemini, un asistente IA eficiente. Responde de forma BREVE y PRECISA. Máximo 3-4 oraciones. Poca introducciones y minimas explicaciones. Directo al punto. Responde en español.\n\nPregunta: " . $message;
    
    // Hacer la solicitud a Gemini
    $response = callGeminiAPI($contextualMessage, $apiKey, $apiUrl);
    
    // Verificar si hay errores en la respuesta
    if (isset($response['error'])) {
        logError('Error de Gemini API: ' . json_encode($response['error']));
        echo json_encode(['success' => false, 'message' => 'Error al comunicarse con Gemini: ' . $response['error']]);
        exit;
    }
    
    // Extraer y formatear la respuesta
    $responseText = extractTextFromResponse($response);
    $formattedResponse = formatResponse($responseText);
    
    // Verificar que la respuesta no esté vacía
    if (empty(trim(strip_tags($formattedResponse)))) {
        echo json_encode(['success' => false, 'message' => 'Gemini no pudo generar una respuesta']);
        exit;
    }
    
    // Enviar la respuesta exitosa
    echo json_encode([
        'success' => true, 
        'message' => $formattedResponse,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    
} catch (Exception $e) {
    logError('Excepción capturada: ' . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error interno del servidor']);
}
?>

