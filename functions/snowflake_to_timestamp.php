<?php

// получает время регистрации из дискорда
if (!function_exists('snowflakeToTimestamp')) {
    function snowflakeToTimestamp($snowflake) {
        return date('Y-m-d H:i:s', (($snowflake / 4194304) + 1420070400000) / 1000);
    }
}
?>
