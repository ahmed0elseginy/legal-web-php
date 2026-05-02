<?php
require_once __DIR__ . "/../includes/auth.php";

$conn = getDb();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_role"] = $user["role"];

            // Set cookie if "remember me" is checked (M3)
            if (isset($_POST["remember"])) {
                // Feature: Pre-fill cookies (requested by user)
                setcookie("remember_email", $email, time() + (86400 * 30), "/");
                setcookie("remember_password", $password, time() + (86400 * 30), "/");

                $token = bin2hex(random_bytes(32)); // Generate secure random token
                setcookie("remember_token", $token, time() + (86400 * 30), "/"); // 30 days
                
                $updateStmt = $conn->prepare("UPDATE users SET remember_token = ? WHERE id = ?");
                $updateStmt->bind_param("si", $token, $user["id"]);
                $updateStmt->execute();
            } else {
                // Clear cookies if not remembered
                setcookie("remember_email", "", time() - 3600, "/");
                setcookie("remember_password", "", time() - 3600, "/");
            }

            if ($user["role"] === "lawyer") {
                header("Location: " . SITE_URL . "/pages/lawyer/dashboard.php");
            } else {
                header("Location: " . SITE_URL . "/pages/client/dashboard.php");
            }
            exit;
        }
    }

    setFlash("البريد الإلكتروني أو كلمة المرور غير صحيحة", "error");
    header("Location: " . SITE_URL . "/pages/login.php");
    exit;
}

header("Location: " . SITE_URL . "/pages/login.php");
exit;
