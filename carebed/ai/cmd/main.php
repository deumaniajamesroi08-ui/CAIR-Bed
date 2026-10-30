<?php

function carebed_ai_load_env_value(array $names): string {
    foreach ($names as $name) {
        $value = getenv($name);
        if ($value !== false && trim($value) !== '') {
            return trim($value);
        }
    }

    $envFiles = [
        __DIR__ . '/../.env',
        __DIR__ . '/../../.env'
    ];

    foreach ($envFiles as $file) {
        if (!is_file($file)) {
            continue;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) {
            continue;
        }

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
                continue;
            }

            [$key, $value] = array_map('trim', explode('=', $line, 2));
            if (in_array($key, $names, true) && $value !== '') {
                return trim($value, "\"'");
            }
        }
    }

    return '';
}

function carebed_ai_as_int($value): int {
    if (is_numeric($value)) {
        return (int) round((float) $value);
    }

    return 0;
}

function carebed_ai_as_float($value): float {
    if (is_numeric($value)) {
        return (float) $value;
    }

    return 0.0;
}

function carebed_ai_clean_list($value): array {
    if (is_array($value)) {
        $items = $value;
    } else {
        $text = trim((string) $value);
        if ($text === '' || strcasecmp($text, 'none reported') === 0) {
            return [];
        }
        $items = explode(',', $text);
    }

    $cleaned = [];
    foreach ($items as $item) {
        $item = trim((string) $item);
        if ($item !== '') {
            $cleaned[] = $item;
        }
    }

    return $cleaned;
}

function carebed_ai_contains_any(string $value, array $needles): bool {
    $value = strtolower(trim($value));
    foreach ($needles as $needle) {
        if (str_contains($value, strtolower($needle))) {
            return true;
        }
    }

    return false;
}

function carebed_ai_infer_state(string $presence): string {
    $presence = strtolower(trim($presence));
    if (in_array($presence, ['not detected', 'no', 'absent', '0'], true)) {
        return 'patient not detected on bed';
    }

    return 'resting / latest bed reading';
}

function carebed_ai_infer_symptoms(int $bpm, float $temperature, string $wetness, string $presence): array {
    $symptoms = [];

    if ($bpm > 0 && $bpm < 60) {
        $symptoms[] = 'low heart rate detected';
    }
    if ($bpm > 100) {
        $symptoms[] = 'elevated heart rate detected';
    }
    if ($temperature >= 38) {
        $symptoms[] = 'high temperature detected';
    }
    if ($temperature > 0 && $temperature < 35.5) {
        $symptoms[] = 'low temperature detected';
    }
    if (carebed_ai_contains_any($wetness, ['wet', 'moist', 'damp', 'detected', 'yes', '1'])) {
        $symptoms[] = 'wetness detected';
    }
    if (carebed_ai_contains_any($presence, ['not detected', 'absent', 'no', '0'])) {
        $symptoms[] = 'patient presence not detected';
    }

    return $symptoms ?: ['none automatically detected from latest sensors'];
}

function carebed_ai_normalize_payload(array $data): array {
    $age = carebed_ai_as_int($data['patient_age'] ?? $data['age'] ?? 0);
    $bpm = carebed_ai_as_int($data['current_bpm'] ?? $data['heart_rate'] ?? 0);
    $temperature = carebed_ai_as_float($data['temperature'] ?? 0);
    $wetness = trim((string) ($data['wetness'] ?? 'unknown'));
    $presence = trim((string) ($data['presence_detection'] ?? 'unknown'));
    $state = trim((string) ($data['state'] ?? ''));
    $duration = trim((string) ($data['duration'] ?? ''));
    $symptoms = carebed_ai_clean_list($data['symptoms'] ?? []);

    if ($state === '') {
        $state = carebed_ai_infer_state($presence);
    }

    if ($duration === '') {
        $durationMinutes = carebed_ai_as_int($data['duration_minutes'] ?? 0);
        $duration = $durationMinutes > 0 ? $durationMinutes . ' minutes' : 'latest sensor reading';
    }

    if (!$symptoms) {
        $symptoms = carebed_ai_infer_symptoms($bpm, $temperature, $wetness, $presence);
    }

    return [
        'patient_name' => trim((string) ($data['patient_name'] ?? '')),
        'patient_age' => $age > 0 ? (string) $age : 'unknown',
        'current_bpm' => $bpm,
        'temperature_celsius' => $temperature > 0 ? number_format($temperature, 1, '.', '') : '',
        'wetness' => $wetness !== '' ? $wetness : 'unknown',
        'presence_detection' => $presence !== '' ? $presence : 'unknown',
        'state' => $state,
        'duration' => $duration,
        'symptoms' => $symptoms
    ];
}

function carebed_ai_analyze_payload(array $payload): array {
    if (!function_exists('curl_init')) {
        throw new RuntimeException('PHP cURL extension is not enabled.');
    }

    $apiKey = carebed_ai_load_env_value(['AI', 'API_KEY', 'GEMINI_KEY']);
    if ($apiKey === '') {
        throw new RuntimeException('AI, API_KEY, or GEMINI_KEY is not set.');
    }

    $normalized = carebed_ai_normalize_payload($payload);
    $prompt = "Analyze this live Carebed sensor reading. Use the provided context and inferred symptoms, but do not diagnose:\n" .
        json_encode($normalized, JSON_PRETTY_PRINT);

    $request = [
        'systemInstruction' => [
            'parts' => [[
                'text' => 'You are Carebed AI, a backend analysis engine for a health app. Analyze heart rate, temperature, wetness, and presence data. CRITICAL: You are not a doctor. Never make a definitive diagnosis. Include a medical disclaimer.'
            ]]
        ],
        'contents' => [[
            'role' => 'user',
            'parts' => [[
                'text' => $prompt
            ]]
        ]],
        'generationConfig' => [
            'temperature' => 0.1,
            'responseMimeType' => 'application/json',
            'responseSchema' => [
                'type' => 'OBJECT',
                'properties' => [
                    'analysis' => ['type' => 'STRING'],
                    'severity_level' => ['type' => 'STRING'],
                    'potential_benign_causes' => [
                        'type' => 'ARRAY',
                        'items' => ['type' => 'STRING']
                    ],
                    'potential_illnesses' => [
                        'type' => 'ARRAY',
                        'items' => ['type' => 'STRING']
                    ],
                    'action_plan' => ['type' => 'STRING'],
                    'medical_disclaimer' => ['type' => 'STRING']
                ]
            ]
        ]
    ];

    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=' . rawurlencode($apiKey);
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($response === false || $httpCode < 200 || $httpCode >= 300) {
        throw new RuntimeException('Gemini API request failed. HTTP ' . $httpCode . ($error ? ': ' . $error : ': ' . $response));
    }

    $decoded = json_decode($response, true);
    $text = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? '';
    $aiResponse = json_decode($text, true);

    if (!is_array($aiResponse)) {
        $aiResponse = ['analysis' => $text !== '' ? $text : $response];
    }

    return [
        'normalized_payload' => $normalized,
        'ai_response' => $aiResponse
    ];
}

if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
    header('Content-Type: application/json');

    $raw = file_get_contents('php://input');
    $payload = json_decode($raw ?: '{}', true);

    if (!is_array($payload)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON payload']);
        exit;
    }

    try {
        $result = carebed_ai_analyze_payload($payload);
        echo json_encode($result['ai_response']);
    } catch (Throwable $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
?>
