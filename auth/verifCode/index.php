<?php
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

    require_once '../db/index.php';

    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? '';
    $code  = $data['code']  ?? '';
    if (!$email || !$code) {
        http_response_code(400);
        exit(json_encode(["error"=>"Data tidak lengkap."]));
    }

    $stmt = $pdo->prepare("SELECT verification_code, code_expiry FROM users WHERE email=?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user) {
        http_response_code(400);
        exit(json_encode(["error"=>"Email tidak terdaftar."]));
    }

    if ($user['verification_code'] !== $code) {
        http_response_code(400);
        exit(json_encode(["error"=>"Kode verifikasi salah."]));
    }
    if (new DateTime() > new DateTime($user['code_expiry'])) {
        http_response_code(400);
        exit(json_encode(["error"=>"Kode verifikasi kadaluarsa."]));
    }

    // $pdo->prepare("UPDATE users SET verified=1, verification_code=NULL, code_expiry=NULL WHERE email=?")
    //     ->execute([$email]);
    $pdo->prepare("UPDATE users SET verified=1, verification_code=NULL, code_expiry=NULL WHERE email=? LIMIT 1")
    ->execute([$email]);

    echo json_encode(["success"=>true, "message"=>"Akun berhasil diverifikasi."]);
