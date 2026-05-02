<?php
require_once __DIR__ . "/../includes/auth.php";

$conn = getDb();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $role = $_POST["role"] ?? "";
    $password = $_POST["password"] ?? "";

    if (empty($name) || empty($email) || empty($password) || !in_array($role, ["lawyer", "client"])) {
        setFlash("جميع الحقول مطلوبة", "error");
        header("Location: " . SITE_URL . "/pages/register.php");
        exit;
    }

    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        setFlash("البريد الإلكتروني مسجل مسبقاً", "error");
        header("Location: " . SITE_URL . "/pages/register.php");
        exit;
    }

    $hashed = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $hashed, $role);
    $stmt->execute();

    setFlash("تم التسجيل بنجاح. قم بتسجيل الدخول", "success");
    header("Location: " . SITE_URL . "/pages/login.php");
    exit;
}

header("Location: " . SITE_URL . "/pages/register.php");
exit;
