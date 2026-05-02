<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole("lawyer");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $price = $_POST["price"] ?? 0;
    $description = trim($_POST["description"] ?? "");
    $lawyer_id = $_SESSION["user_id"];

    if (empty($name) || $price <= 0) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $conn = getDb();
    $lawyer_name = $_SESSION["user_name"];
    $stmt = $conn->prepare("INSERT INTO services (name, price, description, lawyer_id, lawyer_name, status) VALUES (?, ?, ?, ?, ?, 'published')");
    $stmt->bind_param("sdsis", $name, $price, $description, $lawyer_id, $lawyer_name);

    $stmt->execute();
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
} else {
    header("Location: " . SITE_URL);
    exit;
}
