<?php
// версия: загорелый банан 1.0

if (!function_exists('getUserData')) {
    function getUserData($userId, $config) {
        // Подключение к базе данных
        $mysqli = new mysqli($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);

        // Проверка соединения
        if ($mysqli->connect_error) {
            die('Ошибка подключения (' . $mysqli->connect_errno . ') ' . $mysqli->connect_error);
        }

        // Получение данных пользователя
        $result = $mysqli->query("SELECT * FROM user_data WHERE user_id = '$userId'");

        if ($result->num_rows > 0) {
            $userData = $result->fetch_assoc();
            $userData['user_name'] = htmlspecialchars($userData['user_name']);
            $userData['servers'] = htmlspecialchars($userData['servers']);
            $userData['roles'] = nl2br(htmlspecialchars($userData['roles']));
            $userData['registration_time'] = htmlspecialchars($userData['registration_time']);
        } else {
            $userData = null;
        }

        // Закрытие соединения
        $mysqli->close();

        return $userData;
    }
}
?>
