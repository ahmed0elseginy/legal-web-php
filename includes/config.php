<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');

define("DB_HOST", getenv("DB_HOST") ?: "db");
define("DB_USER", getenv("DB_USER") ?: "root");
define("DB_PASS", getenv("DB_PASS") ?: "rootpassword");
define("DB_NAME", getenv("DB_NAME") ?: "singup_task");
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
$base_path = "";
define("SITE_URL", $protocol . "://" . $host . $base_path);

$conn = null;

function getDb()
{
    global $conn;
    if ($conn === null) {
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($conn->connect_error) {
                throw new Exception("Connection failed");
            }
            $conn->set_charset("utf8mb4");
        } catch (Exception $e) {
            http_response_code(500);
            include __DIR__ . "/../pages/error.php";
            exit;
        }
    }
    return $conn;
}
