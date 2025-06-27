<?php
session_start();

// Подключение к БД
require_once 'db.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

try {
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['client_id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['is_admin'] = $user['is_admin'];

        // Редирект в зависимости от роли
        if ($user['is_admin']) {
            header('Location: index.php');
        } else {
            header('Location: ../account.html');
        }
        exit;
    } else {
        echo "Неверный логин или пароль";
    }
} catch (PDOException $e) {
    echo "Ошибка БД: " . $e->getMessage();
}
