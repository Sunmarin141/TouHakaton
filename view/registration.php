<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
    <link rel="stylesheet" href="../css/auth.css">
</head>
<body>

<div class="auth-container">
    <h1 class="auth-title">Регистрация</h1>

    <form action="../controller/registrationUser.php" method="POST">
        <input type="text" placeholder="Введите ник" name="username" required>
        <input type="password" placeholder="Введите пароль" name="password" required>

        <button type="submit" class="auth-btn">Создать аккаунт</button>
    </form>

    <a class="auth-link" href="login.php">У меня уже есть аккаунт</a>
</div>

</body>
</html>
