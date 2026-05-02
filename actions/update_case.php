<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole("lawyer");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $case_id = $_POST["case_id"] ?? null;
    $title = trim($_POST["title"] ?? "");
    $client_name = trim($_POST["client_name"] ?? "");
    $case_type = trim($_POST["case_type"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $lawyer_id = $_SESSION["user_id"];

    if (!$case_id || empty($title) || empty($client_name)) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $conn = getDb();
    $stmt = $conn->prepare("UPDATE cases SET title = ?, client_name = ?, case_type = ?, description = ? WHERE id = ? AND lawyer_id = ?");
    $stmt->bind_param("ssssii", $title, $client_name, $case_type, $description, $case_id, $lawyer_id);
    $stmt->execute();

    header("Location: " . SITE_URL . "/pages/lawyer/cases.php");
    exit;
} else {
    header("Location: " . SITE_URL);
    exit;
}
