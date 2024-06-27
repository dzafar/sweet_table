<?php
// версия: загорелый банан 1.0

if (!function_exists('getUserInfo')) {
    function getUserInfo($accessToken) {
        $url = 'https://discord.com/api/users/@me';
        $options = [
            'http' => [
                'header' => "Authorization: Bearer $accessToken\r\n"
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        if ($response === false) {
            die('Ошибка получения информации о пользователе');
        }

        return json_decode($response, true);
    }
}
?>
