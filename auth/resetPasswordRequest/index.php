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

    $data  = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'] ?? '';

    if (!$email) {
        http_response_code(400);
        echo json_encode(["error" => "Email wajib diisi."]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if (!$user) {
        http_response_code(404);
        echo json_encode(["error" => "Email tidak terdaftar."]);
        exit;
    }

    $token  = bin2hex(random_bytes(32));
    $expiry = date('Y-m-d H:i:s', time() + 3600);
    $pdo->prepare("UPDATE users SET reset_token = ?, reset_expiry = ? WHERE email = ?")
        ->execute([$token, $expiry, $email]);

    $link = sprintf(
        "http://localhost:3000/auth/resetPassword?token=%s&email=%s",
        urlencode($token),
        urlencode($email)
    );

    $html = <<<HTML
    <!doctype html>
    <html>
    <head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    </head>
    <body style="margin:0;padding:0;background:#f4f7fa;font-family:Arial,sans-serif;">
    <table width="100%" bgcolor="#f4f7fa" cellpadding="0" cellspacing="0">
        <tr><td align="center">
        <table width="600" bgcolor="#fff" style="border-radius:8px;overflow:hidden;margin:40px 0;box-shadow:0 2px 6px rgba(0,0,0,0.1);">
            <tr>
            <td align="center" style="padding:20px;background:#4A90E2;color:#fff;">
                <h1 style="margin:0;font-size:24px;">Reset Password</h1>
            </td>
            </tr>
            <tr>
            <td style="padding:30px;color:#333;line-height:1.5;">
                <p>Halo,</p>
                <p>Anda baru saja meminta untuk mereset password akun Anda. Klik tombol di bawah untuk melanjutkan:</p>
                <p style="text-align:center;margin:30px 0;">
                <a href="$link"
                    style="display:inline-block;padding:12px 24px;background:#4A90E2;color:#fff;
                            text-decoration:none;border-radius:4px;font-weight:bold;font-size:16px;"
                    target="_blank" rel="noopener">
                    Reset Password
                </a>
                </p>
                <p>Link ini hanya berlaku selama 1 jam sejak diterbitkan.</p>
                <hr style="border:none;border-top:1px solid #eee;margin:30px 0;"/>
                <p style="font-size:12px;color:#999;">Jika Anda tidak melakukan permintaan ini, abaikan email ini.</p>
            </td>
            </tr>
            <tr>
            <td align="center" style="padding:20px;font-size:12px;color:#aaa;">
                &copy; <?= date('Y') ?> PustakaKu. All rights reserved.
            </td>
            </tr>
        </table>
        </td></tr>
    </table>
    </body>
    </html>
    HTML;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'hendriansyahrizkysetiawan@gmail.com';
        $mail->Password   = 'whqedhvrscuhaeeu';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;
        $mail->CharSet    = 'UTF-8';
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
                'allow_self_signed'=> true
            ]
        ];

        $mail->setFrom('hendriansyahrizkysetiawan@gmail.com', 'No‑Reply PustakaKu');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Reset Password Akun Anda';
        $mail->msgHTML($html);
        
        $mail->AltBody = "Klik link berikut untuk mereset password Anda:\n\n{$link}\n\nLink berlaku 1 jam.";

        $mail->send();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Gagal mengirim email: ' . $mail->ErrorInfo]);
    }
