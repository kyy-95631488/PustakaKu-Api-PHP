<?php
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type");

    require_once '../db/index.php';

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit(0);
    }

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['name'], $data['email'], $data['password'], $data['confirmPassword'])) {
        http_response_code(400);
        echo json_encode(["error" => "Data tidak lengkap."]);
        exit;
    }

    $name = trim($data['name']);
    $email = trim($data['email']);
    $password = $data['password'];
    $confirmPassword = $data['confirmPassword'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        http_response_code(400);
        echo json_encode(["error" => "Email sudah terdaftar."]);
        exit;
    }

    if ($password !== $confirmPassword) {
        http_response_code(400);
        echo json_encode(["error" => "Password dan konfirmasi tidak cocok."]);
        exit;
    }

    $salt = bin2hex(random_bytes(32));
    $hashedPassword = hash('sha512', $password . $salt);

    try {
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, salt) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword, $salt]);

        echo json_encode(["success" => true, "message" => "Registrasi berhasil."]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Gagal mendaftar: " . $e->getMessage()]);
    }
?>
