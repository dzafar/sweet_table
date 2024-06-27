<?php

if (!function_exists('updateRealRolesInDatabase')) {
    function updateRealRolesInDatabase($userId, $servers, $roles) {
        $config = require 'config.php';
        $mysqli = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

        // Проверка соединения
        if ($mysqli->connect_error) {
            die('Ошибка подключения (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
        }

        // Убедимся, что $roles является массивом
        if (!is_array($roles)) {
            $roles = [];
        }

        // Форматирование данных о ролях
        $formattedRoles = [];
        foreach ($roles as $server => $roleList) {
            if (empty($roleList)) {
                $formattedRoles[] = "$server; роли не найдены .";
            } else {
                $formattedRoles[] = "$server; " . implode(', ', $roleList) . " .";
            }
        }
        $rolesText = implode("\n", $formattedRoles);

        // Обновление данных пользователя в базе данных
        $stmt = $mysqli->prepare("UPDATE user_data SET servers = ?, roles = ? WHERE user_id = ?");
        $stmt->bind_param('sss', $servers, $rolesText, $userId);
        $stmt->execute();
        $stmt->close();

        // Закрытие соединения
        $mysqli->close();
    }
}
?>
