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
    require '../../vendor/autoload.php';

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? '';
    if (!$email) {
        http_response_code(400);
        exit(json_encode(["error"=>"Email tidak ada."]));
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email=?");
    $stmt->execute([$email]);
    if (!$stmt->fetch()) {
        http_response_code(400);
        exit(json_encode(["error"=>"Email tidak terdaftar."]));
    }

    $code = rand(100000, 999999);
    $expiry = date('Y-m-d H:i:s', time()+10*60); // 10 menit
    $pdo->prepare("UPDATE users SET verification_code=?, code_expiry=? WHERE email=?")
        ->execute([$code, $expiry, $email]);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'hendriansyahrizkysetiawan@gmail.com';
        $mail->Password = 'whqedhvrscuhaeeu';
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('hendriansyahrizkysetiawan@gmail.com', 'No-Reply');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Kode Verifikasi Akun Anda';
        $mail->Body = '
        <div style="font-family: Arial, sans-serif; max-width: 500px; margin: auto; border: 1px solid #eee; padding: 20px; border-radius: 10px; background-color: #f9f9f9;">
            <h2 style="color:rgb(76, 111, 175); text-align: center;">Kode Verifikasi</h2>
            <p>Halo,</p>
            <p>Berikut adalah kode verifikasi untuk akun Anda:</p>
            <div style="text-align: center; margin: 20px 0;">
                <span style="display: inline-block; font-size: 28px; font-weight: bold; background: rgb(76, 111, 175); color: white; padding: 10px 20px; border-radius: 5px;">' . $code . '</span>
            </div>
            <p>Kode ini berlaku selama <strong>10 menit</strong>.</p>
            <p>Jika Anda tidak merasa meminta kode ini, silakan abaikan email ini.</p>
            <p style="margin-top: 30px; font-size: 12px; color: #888;">Email ini dikirim secara otomatis. Mohon untuk tidak membalas.</p>
        </div>';

        $mail->send();
        echo json_encode(["success"=>true, "message"=>"Kode verifikasi dikirim."]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(["error"=>"Gagal kirim email: " . $mail->ErrorInfo]);
    }
