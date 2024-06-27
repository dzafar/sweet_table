<?php
// версия: загорелый банан 1.0

$config = require 'config.php';
require 'discord_logic.php';


require 'functions/store_user_session.php';
require 'functions/decrypt_user_id.php';

$servers = null;
$roles = null;
$cookieUserId = null;
$userName = null;
$registrationTime = null;

if (isset($_COOKIE['PHPSESSID'])) {
    $key = $config['secret_key']; // Получение секретного ключа из конфигурации
    echo $_COOKIE['PHPSESSID'];
    $cookieUserId = decryptUserId($_COOKIE['PHPSESSID'], $key);


    if ($cookieUserId !== null) {
        // Подключение к базе данных
        $userData = getUserData($cookieUserId, $config);

        if ($userData) {
            $userName = $userData['user_name'];
            $servers = $userData['servers'];
            $roles = $userData['roles'];
            $registrationTime = $userData['registration_time'];
        }
    } else {
        error_log("Ошибка расшифровки cookieUserId");
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Информация о пользователе - Версия: Загорелый банан 1.0</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <?php if (isset($userName)): ?>
            <div class="info">
                <h1>Информация о пользователе</h1>
                <p><strong>Никнейм:</strong> <?php echo $userName; ?></p>
                <p><strong>Сервера:</strong> <?php echo $servers; ?></p>
                <p><strong>Роли:</strong> <?php echo $roles; ?></p>
                <p><strong>Время регистрации:</strong> <?php echo $registrationTime; ?></p>
            </div>
            <?php if (strpos($roles, 'Администратор') !== false): ?>
                <div class="admin-content">
                    <h2>Контент для администраторов</h2>
                    <p>Здесь находится контент, доступный только администраторам.</p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <h1>Нет данных для отображения</h1>
            <h2>Авторизация через Discord</h2>
            <a href="https://discord.com/api/oauth2/authorize?client_id=<?php echo $config['client_id']; ?>&redirect_uri=<?php echo urlencode($config['redirect_uri']); ?>&response_type=code&scope=identify%20guilds" class="button">
                Авторизоваться через Discord
            </a>

            <hr><hr><hr>
        <?php endif; ?>

    </div>
</body>
</html>