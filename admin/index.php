<?php
require 'db.php';

$tables = [];

try {
    $stmt = $pdo->query("SHOW TABLES");
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $tables[] = $row[0];
    }
} catch (PDOException $e) {
    die("Ошибка при получении таблиц: " . $e->getMessage());
}

// Вывод таблиц (для теста)
foreach ($tables as $table) {
    echo "Таблица: " . htmlspecialchars($table) . "<br>";
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Админ-панель - Список таблиц</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="container">
    <h1>Админ-панель</h1>
    <h2>Список таблиц</h2>
    <ul>
        <?php foreach ($tables as $table): ?>
            <li><a href="table.php?table=<?= htmlspecialchars($table) ?>"><?= htmlspecialchars($table) ?></a></li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
