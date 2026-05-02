<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole("lawyer");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $service_id = $_POST["service_id"] ?? null;
    $lawyer_id = $_SESSION["user_id"];

    if (!$service_id) {
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $conn = getDb();
    
    // Get current status
    $stmt = $conn->prepare("SELECT status FROM services WHERE id = ? AND lawyer_id = ?");
    $stmt->bind_param("ii", $service_id, $lawyer_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
        $new_status = ($service['status'] === 'published') ? 'draft' : 'published';
        
        $updateStmt = $conn->prepare("UPDATE services SET status = ? WHERE id = ? AND lawyer_id = ?");
        $updateStmt->bind_param("sii", $new_status, $service_id, $lawyer_id);
        $updateStmt->execute();
    }

    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
} else {
    header("Location: " . SITE_URL);
    exit;
}
