<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole("lawyer");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $service_id = $_POST["service_id"] ?? null;
    $lawyer_id = $_SESSION["user_id"];

    if (!$service_id) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $conn = getDb();
    $stmt = $conn->prepare("DELETE FROM services WHERE id = ? AND lawyer_id = ?");
    $stmt->bind_param("ii", $service_id, $lawyer_id);
    $stmt->execute();

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
} else {
    header("Location: " . SITE_URL);
    exit;
}
