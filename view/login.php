<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход</title>
    <link rel="stylesheet" href="../css/auth.css">
</head>
<body>

<div class="auth-container">
    <h1 class="auth-title">Вход в аккаунт</h1>

    <form action="../controller/loginUser.php" method="POST">
        <input type="text" placeholder="Введите ваш ник" name="username" required>
        <input type="password" placeholder="Введите ваш пароль" name="password" required>

        <button type="submit" class="auth-btn">Войти</button>
    </form>

    <a class="auth-link" href="registration.php">У меня нет аккаунта</a>
</div>

</body>
</html>
