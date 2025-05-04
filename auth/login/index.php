<?php

    if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header("Access-Control-Allow-Credentials: true");
        header("Access-Control-Max-Age: 86400");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
            header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
        }
        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
        }
        exit(0);
    }

    header("Content-Type: application/json");

    require_once '../db/index.php';
    require_once 'jwt.php';

    $data = json_decode(file_get_contents("php://input"), true);
    if (!isset($data['email'], $data['password'])) {
        http_response_code(400);
        echo json_encode(["error" => "Data tidak lengkap."]);
        exit;
    }

    $email = trim($data['email']);
    $password = $data['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user) {
        http_response_code(400);
        echo json_encode(["error" => "Email atau password salah."]);
        exit;
    }

    $checkPassword = hash('sha512', $password . $user['salt']);
    if ($checkPassword !== $user['password']) {
        http_response_code(400);
        echo json_encode(["error" => "Email atau password salah."]);
        exit;
    }

    $token = generateJWT($user['id']);

    $pdo->prepare("UPDATE users SET session_token = ?, token_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE email = ?")
        ->execute([$token, $email]);
    $pdo->prepare("UPDATE users SET last_login = NOW() WHERE email = ?")
        ->execute([$email]);

    if ($user['verified'] == 0) {
        echo json_encode(["success" => false, "unverified" => true]);
        exit;
    }

    echo json_encode([
        "success"    => true,
        "message"    => "Login berhasil.",
        "token"      => $token,
        "expires_in" => 3600
    ]);


    function generateJWT($userId) {
        $header  = json_encode(['typ'=>'JWT','alg'=>'HS256']);
        $payload = json_encode(['userId'=>$userId,'exp'=>time()+3600]);

        $b64h = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
        $b64p = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');
        $sig  = hash_hmac('sha256', "$b64h.$b64p", 'KennyChanUwU', true);
        $b64s = rtrim(strtr(base64_encode($sig), '+/', '-_'), '=');

        return "$b64h.$b64p.$b64s";
    }
