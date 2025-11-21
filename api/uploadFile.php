<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . "/error.log");

header("Content-Type: application/json");
session_start();

require_once "../model/History.php";
require_once "../model/ProcessingAi.php";

if (!isset($_SESSION['user']['id'])) {
    echo json_encode(['error' => 'пользователь не авторизован']);
    exit;
}

$user_id = $_SESSION['user']['id'];

if (!isset($_FILES['file'])) {
    echo json_encode(["error" => "Файл не получен"]);
    exit;
}

$file = $_FILES['file'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo json_encode([
        "error" => "Ошибка загрузки файла",
        "code"  => $file['error']
    ]);
    exit;
}

if (!is_uploaded_file($file['tmp_name'])) {
    echo json_encode([
        "error" => "PHP не считает файл загруженным",
        "tmp"   => $file['tmp_name']
    ]);
    exit;
}

$root = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
$uploadDir = $root . "/uploads/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

if (!is_writable($uploadDir)) {
    echo json_encode([
        "error" => "Uploads недоступна для записи",
        "dir" => $uploadDir
    ]);
    exit;
}

$newFileName = time() . "_" . basename($file['name']);
$absolutePath = $uploadDir . $newFileName;
$relativePath = "uploads/" . $newFileName;

if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
    echo json_encode([
        "error" => "Не удалось сохранить файл",
        "debug" => $absolutePath
    ]);
    exit;
}

$processing = new ProcessingAi();
$result = $processing->processFile($relativePath);

$ext = strtolower(pathinfo($newFileName, PATHINFO_EXTENSION));
$file_type = match ($ext) {
    "jpg", "jpeg", "png" => "image",
    "mp3"                => "audio",
    "mp4", "webm"        => "video",
    default              => "unknown"
};

$history = new History();
$save = $history->saveData(
    $user_id,
    $relativePath,
    $file_type,
    $result['original_text'],
    $result['translated_ru'],
    $result['translated_kz'],
    $result['translated_en'],
    $result['title']
);

try {
    echo json_encode(["success" => true, "data" => $save]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage(),
        "trace" => $e->getTraceAsString()
    ]);
}
