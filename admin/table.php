<?php
require 'db.php';

if (!isset($_GET['table'])) {
    header('Location: index.php');
    exit;
}

$table = $_GET['table'];

// Проверка допустимых таблиц (безопасность)
$allowed_tables = [
    'clients',
    'devices',
    'device_types',
    'fault_components',
    'manufacturers',
    'repair_orders',
    'technicians',
    'users'
];

if (!in_array($table, $allowed_tables)) {
    exit('Недопустимая таблица');
}

// Получаем столбцы таблицы
$stmt = $pdo->query("DESCRIBE `$table`");
$columns = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Обработка CRUD
// Удаление
if (isset($_GET['delete_id'])) {
    $id_column = $columns[0]; // считаем, что первый столбец — PK
    $delete_id = $_GET['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM `$table` WHERE `$id_column` = ?");
    $stmt->execute([$delete_id]);
    header("Location: table.php?table=$table");
    exit;
}

// Добавление записи
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $fields = [];
    $placeholders = [];
    $values = [];
    foreach ($columns as $col) {
        if ($col === $columns[0]) continue; // пропускаем PK с автоинкрементом
        $fields[] = "`$col`";
        $placeholders[] = "?";
        $values[] = $_POST[$col] ?? null;
    }
    $sql = "INSERT INTO `$table` (" . implode(',', $fields) . ") VALUES (" . implode(',', $placeholders) . ")";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($values);
    header("Location: table.php?table=$table");
    exit;
}

// Получаем данные таблицы
$stmt = $pdo->query("SELECT * FROM `$table`");
$rows = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <title>Таблица: <?=htmlspecialchars($table)?></title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<div class="container">
    <h1>Таблица: <?=htmlspecialchars($table)?></h1>
    <p><a href="index.php">← Вернуться к списку таблиц</a></p>

    <table>
        <thead>
        <tr>
            <?php foreach ($columns as $col): ?>
                <th><?=htmlspecialchars($col)?></th>
            <?php endforeach; ?>
            <th>Действия</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <?php foreach ($columns as $col): ?>
                    <td><?=htmlspecialchars($row[$col])?></td>
                <?php endforeach; ?>
                <td>
                    <!-- Для простоты пока только удаление -->
                    <a href="table.php?table=<?=urlencode($table)?>&delete_id=<?=urlencode($row[$columns[0]])?>" onclick="return confirm('Удалить запись?')">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Добавить запись</h2>
    <form method="POST" class="form-inline">
        <?php foreach ($columns as $col):
            if ($col === $columns[0]) continue; // пропускаем PK
            ?>
            <input type="text" name="<?=htmlspecialchars($col)?>" placeholder="<?=htmlspecialchars($col)?>" required>
        <?php endforeach; ?>
        <input type="hidden" name="action" value="add" />
        <input type="submit" value="Добавить">
    </form>
</div>
</body>
</html>
