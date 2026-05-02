<?php
require_once __DIR__ . "/../includes/auth.php";
requireAuth();

$conn = getDb();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $receiverId = (int)($_POST["receiver_id"] ?? 0);
    $content = trim($_POST["content"] ?? "");
    $senderId = $_SESSION["user_id"];

    if (empty($content) || $receiverId <= 0) {
        header("Location: " . $_SERVER["HTTP_REFERER"]);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $senderId, $receiverId, $content);
    $stmt->execute();

    header("Location: " . $_SERVER["HTTP_REFERER"]);
    exit;
}

header("Location: " . SITE_URL . "/pages/login.php");
exit;
