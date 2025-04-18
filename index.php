<?php
session_start();
require_once __DIR__. '/server/db_init.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['username'])) {
    $_SESSION['username'] = trim($_POST['username']);
    header('Location: index.php');
    exit;
}


if (!isset($_SESSION['username'])) {
    ?>
    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <title>Вход в мессенджер</title>
        <link rel="stylesheet" href="css/style.css">
    </head>
    <body>
        <div class="login-container">
            <form action="index.php" method="post">
                <input type="text" name="username" placeholder="Введите ваше имя" required>
                <button type="submit">Войти</button>
            </form>
        </div>
    </body>
    </html>
    <?php
    exit;
}

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Чат</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="chat-container">
        <div class="messages" id="messages"></div>
        <div id="currentUser" data-user="<?php echo htmlspecialchars($_SESSION['username']); ?>"></div>
        <div class="input-area">
            <input type="text" id="messageInput" placeholder="Введите сообщение...">
            <button onclick="sendMessage()">Отправить</button>
        </div>
        <script src="client/script.js"></script>
    </div>
</body>
</html>

<?php