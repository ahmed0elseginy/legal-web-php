<?php
require_once __DIR__ . "/../includes/auth.php";

if (isset($_SESSION["user_id"])) {
    $conn = getDb();
    $userId = $_SESSION["user_id"];
    $updateStmt = $conn->prepare("UPDATE users SET remember_token = NULL WHERE id = ?");
    $updateStmt->bind_param("i", $userId);
    $updateStmt->execute();
}

setcookie("remember_token", "", time() - 3600, "/");
session_destroy();
header("Location: " . SITE_URL . "/pages/login.php");
exit;
