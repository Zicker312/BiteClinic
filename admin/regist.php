<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm'] ?? '';
    $address = ''; // можно добавить поле в форму и сюда

    // Проверки
    if (!$name || !$email || !$phone || !$password || !$confirm) {
        die('Все поля обязательны для заполнения');
    }

    if ($password !== $confirm) {
        die('Пароли не совпадают');
    }

    // Проверка, что email уникален
    $stmt = $pdo->prepare("SELECT client_id FROM clients WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        die('Пользователь с таким email уже существует');
    }

    // Хешируем пароль
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Вставляем в БД
    $stmt = $pdo->prepare("INSERT INTO clients (name, email, phone, address, password_hash) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $email, $phone, $address, $password_hash]);

    // Можно сразу авторизовать
    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['user_name'] = $name;

    header('Location: ../account.html'); // или страница личного кабинета
    exit;
}
?>
