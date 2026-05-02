<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole("lawyer");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $case_id = $_POST["case_id"] ?? null;
    $status = $_POST["status"] ?? null;
    $lawyer_id = $_SESSION["user_id"];

    if (!$case_id || !$status) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $conn = getDb();
    $stmt = $conn->prepare("UPDATE cases SET status = ? WHERE id = ? AND lawyer_id = ?");
    $stmt->bind_param("sii", $status, $case_id, $lawyer_id);

    $stmt->execute();
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
} else {
    header("Location: " . SITE_URL);
    exit;
}
