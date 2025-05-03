<?php
// Cek apakah ini merupakan request dari API
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Endpoint</title>
    <style>
        /* Desain Umum */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
        }

        .container {
            text-align: center;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 40px;
            width: 100%;
            max-width: 600px;
            transition: transform 0.5s ease;
        }

        .container:hover {
            transform: scale(1.05);
        }

        h1 {
            color: #333;
            font-size: 2.5rem;
            margin-bottom: 20px;
            animation: fadeIn 2s ease-out;
        }

        p {
            color: #555;
            font-size: 1.2rem;
            margin: 0;
            opacity: 0;
            animation: fadeIn 3s ease-out forwards;
            animation-delay: 1s;
        }

        /* Animasi */
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Gaya API Icon */
        .api-icon {
            font-size: 5rem;
            color: #3498db;
            margin-bottom: 20px;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-15px);
            }
        }

        .footer {
            margin-top: 30px;
            font-size: 0.9rem;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="api-icon">🔌</div>
        <h1>API Endpoint</h1>
        <p>Web ini hanya berfungsi sebagai endpoint API dan tidak menyediakan tampilan pengguna.</p>
        <div class="footer">
            <p>&copy; 2025 API Service. Semua hak cipta dilindungi.</p>
        </div>
    </div>
</body>
</html>
