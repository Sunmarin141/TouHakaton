<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: view/login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>AI Translate Dashboard</title>
    <link rel="stylesheet" href="css/app.css">
    <script type="module" src="js/history.js" defer></script>
    <script type="module" src="js/modal.js" defer></script>
</head>
<body>

<div class="layout">

    <!-- Левая панель -->
<aside class="sidebar">

    <div class="sidebar-header">
        <h2>История</h2>
        <button id="newChatBtn" class="new-chat-btn">+ Новый чат</button>
    </div>

    <div id="history-list" class="history-list">
        <!-- JS вставит историю -->
    </div>
    <div class="containerExit">
        <a href="controller/exit.php" class="btnExit">exit</a>
    </div>
</aside>


    <main class="content">
        <h2>AI Translate</h2>


        <div id="result-area" class="result-area">
            <!-- Здесь выводится результат обработки -->
        </div>
    </main>

</div>

<div id="modal" class="modal">
    <div class="modal-content">
        <h3>Новый чат</h3>

        <input type="file" id="modalFileInput" />
        <button id="modalProcessBtn" class="primary-btn">Отправить</button>

        <button id="modalCloseBtn" class="close-btn">Закрыть</button>
    </div>
</div>


</body>
</html>
