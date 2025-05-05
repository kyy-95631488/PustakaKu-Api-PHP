<?php
    require_once '../db/index.php';
    require '../../vendor/autoload.php';

    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Max-Age: 86400");
    }
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
        header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        exit(0);
    }
    header("Content-Type: application/json");

    $data = json_decode(file_get_contents("php://input"), true);

    $token = $data['token'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    if (!$token || !$email || !$password) {
        http_response_code(400);
        echo json_encode(["error" => "Semua field wajib diisi."]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT reset_token, reset_expiry, salt FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || $user['reset_token'] !== $token) {
        http_response_code(400);
        echo json_encode(["error" => "Token tidak valid."]);
        exit;
    }

    if (strtotime($user['reset_expiry']) < time()) {
        http_response_code(400);
        echo json_encode(["error" => "Token telah kedaluwarsa."]);
        exit;
    }

    $salt = $user['salt'] ?? bin2hex(random_bytes(32));
    $hashedPassword = hash('sha512', $password . $salt);

    $update = $pdo->prepare("UPDATE users SET password=?, reset_token=NULL, reset_expiry=NULL WHERE email=?");
    $update->execute([$hashedPassword, $email]);

    echo json_encode(["success" => true]);
