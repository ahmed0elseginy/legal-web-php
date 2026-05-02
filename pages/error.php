<?php
require_once __DIR__ . "/../includes/config.php";
$code = $_GET['code'] ?? (http_response_code() ?: '404');
$messages = [
    '401' => 'غير مصرح لك بالدخول',
    '403' => 'الوصول ممنوع',
    '404' => 'الصفحة غير موجودة',
    '500' => 'خطأ داخلي في الخادم'
];
$message = $messages[$code] ?? 'حدث خطأ ما';
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>خطأ <?= $code ?> - MIZAN</title>
    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/css/main.css">
    <style>
        .error-container { text-align: center; padding: 100px 20px; }
        .error-code { font-size: 120px; color: var(--primary); font-weight: 800; margin-bottom: 20px; opacity: 0.2; }
        .error-message { font-size: 24px; margin-bottom: 30px; }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code"><?= $code ?></div>
        <h1 class="error-message"><?= $message ?></h1>
        <p>عذراً، يبدو أن هناك مشكلة في طلبك.</p>
        <br>
        <a href="<?= SITE_URL ?>/index.php" class="btn btn-primary">العودة للرئيسية</a>
    </div>
</body>
</html>
