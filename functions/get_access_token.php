<?php
// версия: загорелый банан 1.0

if (!function_exists('getAccessToken')) {
    function getAccessToken($code, $clientId, $clientSecret, $redirectUri) {
        $url = 'https://discord.com/api/oauth2/token';
        $data = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirectUri
        ];

        $options = [
            'http' => [
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'method' => 'POST',
                'content' => http_build_query($data)
            ]
        ];

        $context = stream_context_create($options);
        $response = file_get_contents($url, false, $context);
        if ($response === false) {
            die('Ошибка получения access_token');
        }

        $result = json_decode($response, true);
        return $result['access_token'];
    }
}
?>
