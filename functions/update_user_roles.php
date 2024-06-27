<?php

require 'functions/get_user_roles.php';
require 'functions/log_user_data.php';

function updateUserRoles($userId, $botToken) {
    $config = require 'config.php';
    $mysqli = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

    // Проверка соединения
    if ($mysqli->connect_error) {
        die('Ошибка подключения (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
    }

    // Получение списка серверов пользователя
    $guilds = getUserGuilds($botToken);

    // Сбор данных о ролях пользователя на каждом сервере
    $userRolesData = [];
    $serverNames = [];
    foreach ($guilds as $guild) {
        $serverNames[] = $guild['name'];

        // Получение ролей пользователя на сервере
        $userRoles = getUserRoles($guild['id'], $userId, $botToken);
        if (empty($userRoles)) {
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
            continue;
        }
        $roles = json_decode($rolesResponse, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
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
        $userRolesData[] = $guild['name'] . ': ' . implode(' ', $userRolesNames);
    }

    // Преобразование данных о серверах и ролях в строки
    $serversText = implode(', ', $serverNames);
    $rolesText = implode("\n", $userRolesData);

    // Обновление данных пользователя в базе данных
    $stmt = $mysqli->prepare("UPDATE user_data SET servers = ?, roles = ? WHERE user_id = ?");
    $stmt->bind_param('sss', $serversText, $rolesText, $userId);
    $stmt->execute();
    $stmt->close();

    // Закрытие соединения
    $mysqli->close();
}
?>
