<?php
session_start();
require_once 'db.php';


// DSN и подключение PDO
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    exit('Ошибка подключения к базе: ' . $e->getMessage());
}

// Получаем данные из POST
$client_id = $_POST['client_id'] ?? null;
$deviceType = $_POST['deviceType'] ?? null;
$service = $_POST['service'] ?? null;
$master = $_POST['master'] ?? null;  // можно использовать для хранения мастера
$date = $_POST['date'] ?? null;
$time = $_POST['time'] ?? null;
$name = $_POST['name'] ?? null;
$phone = $_POST['phone'] ?? null;
$email = $_POST['email'] ?? null;
$booking_date = $_POST['booking_date'] ?? null;
$description = $_POST['description'] ?? null;

// Валидация простая
if (!$client_id || !$service || !$booking_date) {
    exit('Заполните обязательные поля');
}

// Можно сделать дополнительную валидацию, например, по дате, email и т.п.

// Формируем дату и время записи (объединяем дату и время)
$booking_datetime = $booking_date;
if ($time) {
    $booking_datetime .= ' ' . $time . ':00';
}

// Подготовим запрос вставки
$sql = "INSERT INTO booking (client_id, service_name, booking_date, description, created_at)
        VALUES (:client_id, :service_name, :booking_date, :description, NOW())";

$stmt = $pdo->prepare($sql);

try {
    $stmt->execute([
        ':client_id' => $client_id,
        ':service_name' => $service,
        ':booking_date' => $booking_date,
        ':description' => $description
    ]);
} catch (PDOException $e) {
    exit('Ошибка записи в базу: ' . $e->getMessage());
}

// После успешной записи можно редиректить или показывать сообщение
echo 'Заявка успешно отправлена!';
