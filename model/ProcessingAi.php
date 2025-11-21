<?php

require_once 'Database.php';

class ProcessingAi extends Database
{
    private string $apiKey = 'апи ключ =)';

    public function processFile(string $relativePath): array
    {
        $ext = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));

        return match ($ext) {
            'mp3', 'wav', 'm4a', 'ogg' => $this->processAudio($relativePath),
            'mp4', 'webm', 'mkv'       => $this->processVideo($relativePath),
            'txt'                      => $this->processTextFile($relativePath),
            default                    => [
                'original_text' => "Формат не поддерживается: .$ext",
                'translated_ru' => "",
                'translated_kz' => "",
                'translated_en' => "",
                'title'         => "Ошибка формата"
            ]
        };
    }

    private function processAudio(string $relativePath): array
    {
        $absolute = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . $relativePath;

        if (!file_exists($absolute)) {
            return [
                'original_text' => "Аудиофайл не найден: $absolute",
                'translated_ru' => "",
                'translated_kz' => "",
                'translated_en' => "",
                'title'         => "Ошибка аудио"
            ];
        }

        $tempMp3 = $this->reencodeToMp3($absolute, 'audio');
        if ($tempMp3 === null) {
            return [
                'original_text' => "Ошибка перекодирования аудио в MP3",
                'translated_ru' => "",
                'translated_kz' => "",
                'translated_en' => "",
                'title'         => "Ошибка аудио"
            ];
        }

        $tempRelative = 'uploads/' . basename($tempMp3);

        $text = $this->speechToText($tempRelative);

        @unlink($tempMp3);

        $translations = $this->multiTranslate($text);

        return [
            'original_text' => $text,
            'translated_ru' => $translations['ru'] ?? "",
            'translated_kz' => $translations['kz'] ?? "",
            'translated_en' => $translations['en'] ?? "",
            'title'         => "Расшифровка аудио"
        ];
    }

    private function processVideo(string $relativePath): array
    {
        $absolute = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . $relativePath;

        if (!file_exists($absolute)) {
            return [
                'original_text' => "Видео не найдено: $absolute",
                'translated_ru' => "",
                'translated_kz' => "",
                'translated_en' => "",
                'title'         => "Ошибка видео"
            ];
        }

        $tempMp3 = $this->reencodeToMp3($absolute, 'video');
        if ($tempMp3 === null) {
            return [
                'original_text' => "Ошибка конвертации видео в аудио",
                'translated_ru' => "",
                'translated_kz' => "",
                'translated_en' => "",
                'title'         => "Ошибка видео"
            ];
        }

        $tempRelative = 'uploads/' . basename($tempMp3);

        $text = $this->speechToText($tempRelative);

        @unlink($tempMp3);

        $translations = $this->multiTranslate($text);

        return [
            'original_text' => $text,
            'translated_ru' => $translations['ru'] ?? "",
            'translated_kz' => $translations['kz'] ?? "",
            'translated_en' => $translations['en'] ?? "",
            'title'         => "Расшифровка видео"
        ];
    }

    private function processTextFile(string $relativePath): array
    {
        $absolute = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . $relativePath;

        if (!file_exists($absolute)) {
            return [
                'original_text' => "Файл не найден: $absolute",
                'translated_ru' => "",
                'translated_kz' => "",
                'translated_en' => "",
                'title'         => "Ошибка TXT"
            ];
        }

        $contents = file_get_contents($absolute);
        $translations = $this->multiTranslate($contents);

        return [
            'original_text' => $contents,
            'translated_ru' => $translations['ru'] ?? "",
            'translated_kz' => $translations['kz'] ?? "",
            'translated_en' => $translations['en'] ?? "",
            'title'         => "TXT файл"
        ];
    }

    private function reencodeToMp3(string $absolutePath, string $type = 'audio'): ?string
    {
        $root = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
        $uploadsDir = $root . '/uploads';

        if (!is_dir($uploadsDir)) {
            @mkdir($uploadsDir, 0777, true);
        }

        $tempMp3 = $uploadsDir . '/temp_' . $type . '_' . time() . '_' . mt_rand(1000, 9999) . '.mp3';

        $cmd = "ffmpeg -i " . escapeshellarg($absolutePath)
             . " -vn -acodec libmp3lame -ab 64k -ar 44100 "
             . escapeshellarg($tempMp3)
             . " -y 2>&1";

        $out = [];
        $code = 0;
        exec($cmd, $out, $code);

        if ($code !== 0 || !file_exists($tempMp3) || filesize($tempMp3) === 0) {
            return null;
        }

        return $tempMp3;
    }

    private function speechToText(string $relativePath): string
    {
        $absolute = rtrim($_SERVER['DOCUMENT_ROOT'], '/') . '/' . $relativePath;

        if (!file_exists($absolute)) {
            return "Файл не найден в контейнере: $absolute";
        }

        $url = 'https://api.openai.com/v1/audio/transcriptions';

        $postFields = [
            'file'            => new CURLFile($absolute),
            'model'           => 'whisper-1',
            'response_format' => 'verbose_json'
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_POSTFIELDS     => $postFields
        ]);

        $raw   = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($raw === false) {
            return "Ошибка запроса к Whisper: $error";
        }

        $data = json_decode($raw, true);

        if (isset($data['error'])) {
            return "Whisper Error: " . $data['error']['message'];
        }

        if (!empty($data['text']) && is_string($data['text'])) {
            $text = trim($data['text']);
            return $text !== '' ? $text : "Не удалось распознать речь";
        }

        if (!empty($data['segments']) && is_array($data['segments'])) {
            $full = '';
            foreach ($data['segments'] as $seg) {
                if (!empty($seg['text'])) {
                    $full .= $seg['text'] . ' ';
                }
            }
            $full = trim($full);
            return $full !== '' ? $full : "Не удалось распознать речь";
        }

        return "Не удалось распознать речь";
    }

    private function multiTranslate(string $text): array
{
    $text = trim($text);

    if ($text === '') {
        return ['ru' => '', 'kz' => '', 'en' => ''];
    }

    if (strlen($text) > 20000) {
        $text = substr($text, 0, 20000) . "\n\n[Текст обрезан для перевода]";
    }

    $url = "https://api.openai.com/v1/responses";

    $post = [
        "model" => "gpt-4o-mini",
        "input" => [
            [
                "role" => "system",
                "content" => "Ты переводчик. Верни СТРОГО JSON без пояснений."
            ],
            [
                "role" => "user",
                "content" => 
                    "Переведи текст на русский, казахский и английский.\n\n" .
                    "{ \"ru\": \"\", \"kz\": \"\", \"en\": \"\" }\n\n" .
                    "Текст:\n\"\"\"\n" . $text . "\n\"\"\""
            ]
        ],
        "temperature" => 0
    ];

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $this->apiKey,
            'Content-Type: application/json'
        ],
        CURLOPT_POSTFIELDS     => json_encode($post, JSON_UNESCAPED_UNICODE)
    ]);

    $raw   = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    // --- Ошибка CURL ---
    if ($raw === false) {
        file_put_contents(__DIR__ . "/translate_error.log", "CURL ERROR: $error\n", FILE_APPEND);
        return ['ru' => '', 'kz' => '', 'en' => ''];
    }

    $data = json_decode($raw, true);

    // --- Ошибка OpenAI ---
    if (isset($data['error'])) {
        file_put_contents(__DIR__ . "/translate_error.log", "OPENAI ERROR:\n" . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n", FILE_APPEND);
        return ['ru' => '', 'kz' => '', 'en' => ''];
    }

    // --- Извлекаем JSON из ответа (модель может обернуть в markdown) ---
    $content = $data['output'][0]['content'][0]['text'] ?? "";

    if (!preg_match('/\{(?:[^{}]|(?R))*\}/su', $content, $match)) {
        file_put_contents(__DIR__ . "/translate_error.log", "JSON NOT FOUND IN:\n$content\n\n", FILE_APPEND);
        return ['ru' => '', 'kz' => '', 'en' => ''];
    }

    $jsonText = $match[0];
    $json = json_decode($jsonText, true);

    if (!is_array($json)) {
        file_put_contents(__DIR__ . "/translate_error.log", "BAD JSON:\n$jsonText\n\n", FILE_APPEND);
        return ['ru' => '', 'kz' => '', 'en' => ''];
    }

    return [
        'ru' => $json['ru'] ?? '',
        'kz' => $json['kz'] ?? '',
        'en' => $json['en'] ?? ''
    ];
}

}
