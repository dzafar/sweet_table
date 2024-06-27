<?php
// версия: загорелый банан 1.0

if (!function_exists('getUserGuilds')) {
    function getUserGuilds($accessToken) {
        $url = 'https://discord.com/api/users/@me/guilds';
        $options = [
            'http' => [
                'header' => "Authorization: Bearer $accessToken\r\n"
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        if ($response === false) {
            die('Ошибка получения списка серверов пользователя');
        }

        return json_decode($response, true);
    }
}
?>
