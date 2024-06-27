<?php
// версия: загорелый банан 1.0

if (!function_exists('getUserRoles')) {
    function getUserRoles($guildId, $userId, $botToken) {
        $membersUrl = "https://discord.com/api/guilds/{$guildId}/members/{$userId}";
        $membersOptions = [
            'http' => [
                'header' => "Authorization: Bot $botToken\r\n"
            ]
        ];

        $membersContext = stream_context_create($membersOptions);
        $membersResponse = @file_get_contents($membersUrl, false, $membersContext);
        if ($membersResponse === false) {
            return [];
        }
        $member = json_decode($membersResponse, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }

        $userRoles = $member['roles'];

        // Получение списка ролей для каждого сервера с использованием токена бота
        $rolesUrl = "https://discord.com/api/guilds/{$guildId}/roles";
        $rolesOptions = [
            'http' => [
                'header' => "Authorization: Bot $botToken\r\n"
            ]
        ];

        $rolesContext = stream_context_create($rolesOptions);
        $rolesResponse = @file_get_contents($rolesUrl, false, $rolesContext);
        if ($rolesResponse === false) {
            return [];
        }
        $roles = json_decode($rolesResponse, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }

        // Сопоставление ролей пользователя с ролями на сервере
        $userRolesInfo = [];
        foreach ($roles as $role) {
            if (in_array($role['id'], $userRoles)) {
                $userRolesInfo[] = [
                    'id' => $role['id'],
                    'name' => $role['name'],
                    'color' => $role['color'],
                    'permissions' => $role['permissions']
                ];
            }
        }

        return $userRolesInfo;
    }
}
?>
