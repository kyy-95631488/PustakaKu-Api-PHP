<?php
    header("Content-Type: application/json");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE");
    header("Access-Control-Allow-Headers: Content-Type");

    require_once '../db/index.php';
    require_once 'jwt.php';

    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit(0);
    }

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

    $hashedPassword = $user['password'];
    $salt = $user['salt'];
    $checkPassword = hash('sha512', $password . $salt);

    if ($checkPassword !== $hashedPassword) {
        http_response_code(400);
        echo json_encode(["error" => "Email atau password salah."]);
        exit;
    }

    $token = generateJWT($user['id']);
    $expires_in = 3600;

    $stmt = $pdo->prepare("UPDATE users SET session_token = ?, token_expiry = NOW() + INTERVAL 1 HOUR WHERE email = ?");
    $stmt->execute([$token, $email]);

    $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE email = ?");
    $stmt->execute([$email]);

    echo json_encode([
        "success" => true,
        "message" => "Login berhasil.",
        "token" => $token,
        "expires_in" => $expires_in
    ]);

    function generateJWT($userId) {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode(['userId' => $userId, 'exp' => time() + 3600]);

        $base64UrlHeader = rtrim(strtr(base64_encode($header), '+/', '-_'), '=');
        $base64UrlPayload = rtrim(strtr(base64_encode($payload), '+/', '-_'), '=');

        $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", 'your-secret-key', true);
        $base64UrlSignature = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');

        return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
    }
?>
