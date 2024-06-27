<?php
// версия: загорелый банан 1.0

require 'functions/snowflake_to_timestamp.php';
require 'functions/log_user_data.php';
require 'functions/get_access_token.php';
require 'functions/get_user_info.php';
require 'functions/get_user_guilds.php';
require 'functions/get_user_roles.php';
require 'functions/store_user_session.php';
require 'functions/decrypt_user_id.php';
require 'functions/get_user_data.php';

$userId = null; // Инициализация переменной $userId

// Основная логика
if (isset($_GET['code'])) {
    $code = $_GET['code'];
    $config = require 'config.php';
    $clientId = $config['client_id'];
    $clientSecret = $config['client_secret'];
    $redirectUri = $config['redirect_uri'];

    // Получение access_token
    $accessToken = getAccessToken($code, $clientId, $clientSecret, $redirectUri);

    // Получение информации о пользователе
    $user = getUserInfo($accessToken);
    $userId = $user['id'];
    $userName = $user['username'];
    $registrationTime = snowflakeToTimestamp($user['id']);

    // Получение списка серверов пользователя
    $guilds = getUserGuilds($accessToken);

    // Получение токена бота из конфигурации
    $botToken = $config['bot_token'];

    // Сбор данных о ролях пользователя на каждом сервере
    $userRolesData = [];
    $serverNames = [];
    foreach ($guilds as $guild) {
        $serverNames[] = $guild['name'];

        // Получение ролей пользователя на сервере
        $userRoles = getUserRoles($guild['id'], $userId, $botToken);
        if (empty($userRoles)) {
            $userRolesData[] = $guild['name'] . '; роли не найдены .';
            continue;
        }

        // Получение списка ролей для каждого сервера с использованием токена бота
        $rolesUrl = "https://discord.com/api/guilds/{$guild['id']}/roles";
        $rolesOptions = [
            'http' => [
                'header' => "Authorization: Bot $botToken\r\n"
            ]
        ];

        $rolesContext = stream_context_create($rolesOptions);
        $rolesResponse = @file_get_contents($rolesUrl, false, $rolesContext);
        if ($rolesResponse === false) {
            $userRolesData[] = $guild['name'] . '; роли не найдены .';
            continue;
        }
        $roles = json_decode($rolesResponse, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $userRolesData[] = $guild['name'] . '; роли не найдены .';
            continue;
        }

        // Сопоставление ролей пользователя с ролями на сервере
        $userRolesNames = [];
        foreach ($roles as $role) {
            if (in_array($role['id'], $userRoles)) {
                $userRolesNames[] = $role['name'];
            }
        }

        // Форматирование данных о сервере и ролях
        if (empty($userRolesNames)) {
            $userRolesData[] = $guild['name'] . '; роли не найдены .';
        } else {
            $userRolesData[] = $guild['name'] . '; ' . implode(', ', $userRolesNames) . ' .';
        }
    }

    // Преобразование данных о серверах и ролях в строки
    $serversText = implode(', ', $serverNames);
    $rolesText = implode("\n", $userRolesData);

    // Запись данных пользователя
    logUserData($userId, $userName, $serversText, $rolesText, $registrationTime);

    // Сохранение информации о пользователе в сессии
    $key = $config['secret_key']; // Получение секретного ключа из конфигурации
    storeUserSession($userId, $key);


    // Перенаправление на index.php для вывода данных
    echo '<meta http-equiv="refresh" content="3;url=http:index.php" />';
    exit();
} else {
    $config = require 'config.php'; // Подключение конфигурации

    // Получение данных пользователя из куки
    if (isset($_COOKIE['PHPSESSID'])) {
        $key = $config['secret_key']; // Получение секретного ключа из конфигурации
        $userId = decryptUserId($_COOKIE['PHPSESSID'], $key);

        if ($userId !== null) {
            // Получение данных пользователя из базы данных
            $userData = getUserData($userId, $config);

            if ($userData) {
                $userName = $userData['user_name'];
                $servers = $userData['servers'];
                $roles = $userData['roles'];
                $registrationTime = $userData['registration_time'];
            } else {
                $userName = null;
            }
        } else {
            error_log("Ошибка расшифровки user_id");
            $userName = null;
        }
    } else {
        $userName = null;
    }
}
?>
