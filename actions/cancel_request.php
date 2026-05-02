<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole("client");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $request_id = $_POST["request_id"] ?? null;
    $client_id = $_SESSION["user_id"];

    if (!$request_id) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $conn = getDb();
    // Only allow cancelling if status is 'pending'
    $stmt = $conn->prepare("DELETE FROM service_requests WHERE id = ? AND client_id = ? AND status = 'pending'");
    $stmt->bind_param("ii", $request_id, $client_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        setFlash("تم إلغاء الطلب بنجاح", "success");
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
} else {
    header("Location: " . SITE_URL);
    exit;
}
