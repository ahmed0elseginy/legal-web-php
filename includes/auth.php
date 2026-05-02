<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/config.php";

function isLoggedIn() {
    if (isset($_SESSION["user_id"])) {
        return true;
    }
    // Check cookie (M3)
    if (isset($_COOKIE["remember_token"])) {
        $conn = getDb();
        $token = $_COOKIE["remember_token"];
        $stmt = $conn->prepare("SELECT id, name, email, role FROM users WHERE remember_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["user_name"] = $user["name"];
            $_SESSION["user_email"] = $user["email"];
            $_SESSION["user_role"] = $user["role"];
            return true;
        }
    }
    return false;
}

function requireAuth() {
    if (!isLoggedIn()) {
        header("Location: " . SITE_URL . "/pages/login.php");
        exit;
    }
}

function requireRole($role) {
    requireAuth();
    if ($_SESSION["user_role"] !== $role) {
        header("Location: " . SITE_URL . "/pages/login.php");
        exit;
    }
}

function getUser() {
    return [
        "id" => $_SESSION["user_id"] ?? null,
        "name" => $_SESSION["user_name"] ?? null,
        "email" => $_SESSION["user_email"] ?? null,
        "role" => $_SESSION["user_role"] ?? null,
    ];
}

function redirectIfLoggedIn() {
    if (isLoggedIn()) {
        if ($_SESSION["user_role"] === "lawyer") {
            header("Location: " . SITE_URL . "/pages/lawyer/dashboard.php");
        } else {
            header("Location: " . SITE_URL . "/pages/client/dashboard.php");
        }
        exit;
    }
}

function setFlash($message, $type = "success") {
    $_SESSION["flash"] = compact("message", "type");
}

function getFlash() {
    if (isset($_SESSION["flash"])) {
        $flash = $_SESSION["flash"];
        unset($_SESSION["flash"]);
        return $flash;
    }
    return null;
}
