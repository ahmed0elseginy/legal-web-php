<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole("client");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = getDb();
    $requestId = (int)($_POST["request_id"] ?? 0);
    $description = trim($_POST["description"] ?? "");
    $clientId = $_SESSION["user_id"];

    if ($requestId <= 0) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }

    // Ensure the request belongs to the client and is still pending
    $stmt = $conn->prepare("UPDATE service_requests SET description = ? WHERE id = ? AND client_id = ? AND status = 'pending'");
    $stmt->bind_param("sii", $description, $requestId, $clientId);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "تم تحديث الطلب بنجاح";
    } else {
        $_SESSION['error'] = "حدث خطأ أثناء تحديث الطلب";
    }

    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit;
}

header("Location: " . SITE_URL);
exit;
