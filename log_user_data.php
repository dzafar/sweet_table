<?php

if (!function_exists('snowflakeToTimestamp')) {
    // Функция для преобразования Snowflake ID в метку времени
    function snowflakeToTimestamp($snowflake) {
        return date('Y-m-d H:i:s', (($snowflake / 4194304) + 1420070400000) / 1000);
    }
}

if (!function_exists('logUserData')) {
    // Функция для записи данных пользователя в базу данных
    function logUserData($userId, $userName, $serversText, $rolesText, $registrationTime) {
        $config = require 'config.php';
        $mysqli = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

        if ($mysqli->connect_error) {
            die('Ошибка подключения (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
        }

        $stmt = $mysqli->prepare("INSERT INTO user_data (user_id, user_name, servers, roles, registration_time) VALUES (?, ?, ?, ?, ?)");
        if (!$stmt) {
            die('Ошибка подготовки запроса: ' . $mysqli->error);
        }

        $stmt->bind_param('sssss', $userId, $userName, $serversText, $rolesText, $registrationTime);

        if (!$stmt->execute()) {
            die('Ошибка выполнения запроса: ' . $stmt->error);
        }

        $stmt->close();
        $mysqli->close();
    }
}
?>
