<?php
// ID таблицы
$spreadsheetId = '1mE1Ic7v4o9AyXdgaT9enNIlV-iYD60N7kzJSZ1oUSfY';

// URL для экспорта данных в формате CSV
$url = "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/export?format=csv";

// Выполнение запроса
$response = file_get_contents($url);
if ($response === false) {
    die('Error fetching data from Google Sheets');
}

// Преобразование CSV в массив
$rows = array_map('str_getcsv', explode("\n", $response));
$header = array_shift($rows);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Sheets Data</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Данные из Google Sheets</h1>
    <table>
        <thead>
            <tr>
                <?php
                // Вывод заголовков таблицы
                foreach ($header as $col) {
                    echo "<th>" . htmlspecialchars($col) . "</th>";
                }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php
            // Вывод данных таблицы
            foreach ($rows as $row) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>" . htmlspecialchars($cell) . "</td>";
                }
                echo "</tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
