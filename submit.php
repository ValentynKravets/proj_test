<?php
session_start();
require 'db.php';
//  підключення до бд
header('Content-Type: application/json');

function logToCSV($data)
{
    // логування
    $logFile = __DIR__ . '/logs.csv';
    $csvFile = @fopen($logFile, 'a');
    if ($csvFile === false) {
        error_log("Не вдалося відкрити файл logs.csv");
        return;
    }
    if (!file_exists($logFile)) {
        fputcsv($csvFile, ['Дата і час', 'Ім\'я', 'Прізвище', 'Email', 'Телефон', 'Послуга', 'Ціна', 'Коментар', 'FBP', 'ggl', 'Країна', 'Місто', 'IP', 'Результат']);
    }
    fputcsv($csvFile, $data);
    fclose($csvFile);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // робота з  csrf токеном при відправці
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (empty($csrf_token) || $csrf_token !== $_SESSION['csrf_token']) {
        $response = ['success' => false, 'message' => 'Недійсний CSRF-токен'];
        logToCSV(array_merge([date("Y-m-d H:i:s")], array_values($_POST), ['Невдача']));
        echo json_encode($response);
        exit;
    }

    $fbp = trim($_POST['fbp'] ?? '');
    $ggl = trim($_POST['ggl'] ?? '');
    $firstName = trim($_POST['name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $selectService = trim($_POST['select_service'] ?? '');
    $selectPrice = trim($_POST['select_price'] ?? '');
    $comments = trim($_POST['comments'] ?? '');
    $dateTime = date("Y-m-d H:i:s");

    // Отримання геоданих на сервері
    $ip = $_SERVER['REMOTE_ADDR'];
    $country = 'Unknown';
    $city = 'Unknown';
    try {
        // Спроба отриання гео даних через  api
        $geoData = json_decode(file_get_contents("https://ipinfo.io/$ip/json"), true);
        if (isset($geoData['status']) && $geoData['status'] === 'error') {
            throw new Exception("Помилка ipinfo: " . $geoData['message']);
        }
        $country = $geoData['country'] ?? 'Unknown';
        $city = $geoData['city'] ?? 'Unknown';
        $ip = $geoData['ip'] ?? $ip;
        error_log("Геодані з ipinfo: " . print_r($geoData, true));
    } catch (Exception $e) {
        error_log("Помилка ipinfo: " . $e->getMessage());
        // Альтернатива
        try {
            $geoData = json_decode(file_get_contents("http://ip-api.com/json/$ip"), true);
            if (isset($geoData['status']) && $geoData['status'] === 'fail') {
                throw new Exception("Error ip-api: " . $geoData['message']);
            }
            $country = $geoData['country'] ?? 'Невідомо';
            $city = $geoData['city'] ?? 'Невідомо';
            $ip = $geoData['query'] ?? $ip;
            error_log("Geodata з ip-api: " . print_r($geoData, true));
        } catch (Exception $e) {
            error_log("Error ip-api: " . $e->getMessage());
        }
    }
    //  обробка невірних запитів, пусті поля, тощо
    if (empty($firstName) || empty($lastName) || empty($email)) {
        $response = ['success' => false, 'message' => 'Fill in all fields'];
        logToCSV([$dateTime, $firstName, $lastName, $email, $phone, $selectService, $selectPrice, $comments, $fbp, $ggl, $country, $city, $ip, 'Failure']);
        echo json_encode($response);
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response = ['success' => false, 'message' => 'Incorrect email'];
        logToCSV([$dateTime, $firstName, $lastName, $email, $phone, $selectService, $selectPrice, $comments, $fbp, $ggl, $country, $city, $ip, 'Failure']);
        echo json_encode($response);
        exit;
    }

    if (!preg_match('/^\+1\d{10}$/', $phone)) {
        $response = ['success' => false, 'message' => 'Incorrect phone format '];
        logToCSV([$dateTime, $firstName, $lastName, $email, $phone, $selectService, $selectPrice, $comments, $fbp, $ggl, $country, $city, $ip, 'Failure']);
        echo json_encode($response);
        exit;
    }
    $stmt = $pdo->prepare("
        INSERT INTO leads (first_name, last_name, email, phone, select_service, select_price, comments, fbp, ggl, country, city, ip, created_at) 
        VALUES (:first_name, :last_name, :email, :phone, :select_service, :select_price, :comments, :fbp, :ggl, :country, :city, :ip, :created_at)
    ");

    $data = [
        ':first_name' => $firstName,
        ':last_name' => $lastName,
        ':email' => $email,
        ':phone' => $phone,
        ':select_service' => $selectService,
        ':select_price' => $selectPrice,
        ':comments' => $comments,
        ':fbp' => $fbp,
        ':ggl' => $ggl,
        ':country' => $country,
        ':city' => $city,
        ':ip' => $ip,
        ':created_at' => $dateTime
    ];

    if ($stmt->execute($data)) {
        logToCSV([$dateTime, $firstName, $lastName, $email, $phone, $selectService, $selectPrice, $comments, $fbp, $ggl, $country, $city, $ip, 'Success']);
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        echo json_encode([
            'success' => true,
            'message' => 'Thank you for your enquiry! We will contact you shortly.',
            'redirect_url' => 'success.php'
            // переадресація на сторінку дякую
        ]);
    } else {
        $response = ['success' => false, 'message' => 'Server error'];
        logToCSV([$dateTime, $firstName, $lastName, $email, $phone, $selectService, $selectPrice, $comments, $fbp, $ggl, $country, $city, $ip, 'Failure']);
        echo json_encode($response);
    }
} else {
    $response = ['success' => false, 'message' => 'Incorrect request method'];
    logToCSV([date("Y-m-d H:i:s"), '', '', '', '', '', '', '', '', '', '', '', '', 'Failure']);
    echo json_encode($response);
}
