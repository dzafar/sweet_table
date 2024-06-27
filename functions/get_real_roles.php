<?php

require 'functions/get_user_roles.php';

function getRealRoles($userId, $botToken) {
    $config = require 'config.php';
    $guilds = getUserGuilds($botToken);

    if (!is_array($guilds)) {
        // Обработка ошибки, если $guilds не является массивом
        return [
            'servers' => '',
            'roles' => 'Ошибка получения серверов'
        ];
    }

    $userRolesData = [];
    $serverNames = [];
    foreach ($guilds as $guild) {
        $serverNames[] = $guild['name'];

        // Получение ролей пользователя на сервере
        $userRoles = getUserRoles($guild['id'], $userId, $botToken);
        if (!is_array($userRoles) || empty($userRoles)) {
            continue;
        }

        // Форматирование данных о сервере и ролях
        $rolesInfo = [];
        foreach ($userRoles as $role) {
            $rolesInfo[] = "{$role['name']} (ID: {$role['id']}, Color: {$role['color']}, Permissions: {$role['permissions']})";
        }
        $userRolesData[] = $guild['name'] . ': ' . implode(', ', $rolesInfo);
    }

    return [
        'servers' => implode(', ', $serverNames),
        'roles' => implode("\n", $userRolesData)
    ];
}
?>
