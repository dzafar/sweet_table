<?php
if (!function_exists('storeUserSession')) {
    function storeUserSession($decrypted, $key) {
        $ekey = hash('SHA256', $key, true);
        $iv = '1234567890123456'; // Фиксированный IV (16 байт для AES-256-CBC)
        $cipher = "aes-128-cbc";
        $encrypted = openssl_encrypt($decrypted . md5($decrypted), $cipher, $ekey, 0, $iv);
        $iv_base64 = rtrim(base64_encode($iv), '=');

        $session_id = $iv_base64 . base64_encode($encrypted);

        setcookie('PHPSESSID', $session_id, 0, '/', '', false, true); // Время жизни 0, HttpOnly и Secure

        return $session_id;
    }
}
