<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole("client");

$conn = getDb();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $serviceId = (int)($_POST["service_id"] ?? 0);
    $lawyerId = (int)($_POST["lawyer_id"] ?? 0);
    $clientId = $_SESSION["user_id"];

    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->bind_param("i", $serviceId);
    $stmt->execute();
    $service = $stmt->get_result()->fetch_assoc();

    if ($service) {
        $serviceName = $service["name"];
        $clientName = $_SESSION["user_name"];
        $description = trim($_POST["description"] ?? "");
        
        $stmt = $conn->prepare("INSERT INTO service_requests (client_id, lawyer_id, service_id, service_name, client_name, description, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->bind_param("iiisss", $clientId, $lawyerId, $serviceId, $serviceName, $clientName, $description);
        $stmt->execute();
        setFlash("تم إرسال طلب الخدمة بنجاح", "success");
    }

    header("Location: " . SITE_URL . "/pages/client/marketplace.php");
    exit;
}

header("Location: " . SITE_URL . "/pages/client/marketplace.php");
exit;
