<?php

if (!function_exists('decryptUserId')) {
    function decryptUserId($encrypted, $key) {
        $ekey = hash('SHA256', $key, true);
        $iv = base64_decode(substr($encrypted, 0, 22) . '==');
        $encrypted = substr($encrypted, 22);
        $cipher = "aes-128-cbc";
        $decrypted = openssl_decrypt(base64_decode($encrypted), $cipher, $ekey, 0, $iv);
        $hash = substr($decrypted, -32);
        $decrypted = substr($decrypted, 0, -32);
        if (md5($decrypted) != $hash) return false;
        return $decrypted;
    }
}