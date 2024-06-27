<?php
// версия: загорелый банан 1.0

if (!function_exists('logUserData')) {
    function logUserData($userId, $userName, $servers, $roles, $registrationTime) {
        $config = require 'config.php';
        $mysqli = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

        // Проверка соединения
        if ($mysqli->connect_error) {
            die('Ошибка подключения (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
        }

        // Подготовка и выполнение запроса на вставку данных
        $stmt = $mysqli->prepare("INSERT INTO user_data (user_id, user_name, servers, roles, registration_time) VALUES (?, ?, ?, ?, ?)
                                  ON DUPLICATE KEY UPDATE user_name = VALUES(user_name), servers = VALUES(servers), roles = VALUES(roles), registration_time = VALUES(registration_time)");
        $stmt->bind_param('sssss', $userId, $userName, $servers, $roles, $registrationTime);
        $stmt->execute();
        $stmt->close();

        // Закрытие соединения
        $mysqli->close();
    }
}
?>
