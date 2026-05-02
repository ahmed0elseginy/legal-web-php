<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole("lawyer");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $service_id = $_POST["service_id"] ?? null;
    $name = trim($_POST["name"] ?? "");
    $price = $_POST["price"] ?? 0;
    $description = trim($_POST["description"] ?? "");
    $lawyer_id = $_SESSION["user_id"];

    if (!$service_id || empty($name) || $price <= 0) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $conn = getDb();
    $stmt = $conn->prepare("UPDATE services SET name = ?, price = ?, description = ? WHERE id = ? AND lawyer_id = ?");
    $stmt->bind_param("sdsii", $name, $price, $description, $service_id, $lawyer_id);
    $stmt->execute();

    header("Location: " . SITE_URL . "/pages/lawyer/marketplace.php");
    exit;
} else {
    header("Location: " . SITE_URL);
    exit;
}
