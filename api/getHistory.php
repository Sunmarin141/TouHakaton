<?php
header("Content-Type: application/json");

require_once '../model/History.php';
session_start();

$user_id = $_SESSION['user']['id'] ?? null;

if(!isset($user_id)){
    echo json_encode(['error' => 'пользователь не авторизован']);
    exit();
}

$subj = new History();

$allFields = $subj->getHistory($user_id);

echo json_encode($allFields);