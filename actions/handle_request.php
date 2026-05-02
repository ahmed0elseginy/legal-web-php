<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole("lawyer");

$conn = getDb();

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $requestId = (int)($_POST["request_id"] ?? 0);
    $action = $_POST["action"] ?? "";

    if ($requestId > 0 && in_array($action, ['accept', 'reject', 'accepted', 'rejected'])) {
        $status = ($action === 'accept' || $action === 'accepted') ? 'accepted' : 'rejected';
        
        $stmt = $conn->prepare("UPDATE service_requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $requestId);
        $stmt->execute();

        if ($status === 'accepted') {
            // Auto-create a case
            $stmt = $conn->prepare("SELECT sr.*, s.name as service_name FROM service_requests sr JOIN services s ON sr.service_id = s.id WHERE sr.id = ?");
            $stmt->bind_param("i", $requestId);
            $stmt->execute();
            $request = $stmt->get_result()->fetch_assoc();
            
            if ($request) {
                $lawyer_id = $_SESSION["user_id"];
                $lawyer_name = $_SESSION["user_name"];
                $stmt = $conn->prepare("INSERT INTO cases (client_id, lawyer_id, title, client_name, lawyer_name, case_type, description, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'active')");
                $stmt->bind_param("iisssss", $request['client_id'], $lawyer_id, $request['service_name'], $request['client_name'], $lawyer_name, $request['service_name'], $request['description']);
                $stmt->execute();
            }
        }
        
        setFlash("تم " . ($status === 'accepted' ? 'قبول' : 'رفض') . " الطلب بنجاح", "success");
    }

    $redirect = $_SERVER['HTTP_REFERER'] ?? (SITE_URL . "/pages/lawyer/requests.php");
    header("Location: " . $redirect);
    exit;
}
