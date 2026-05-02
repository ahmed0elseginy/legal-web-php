<?php
require_once __DIR__ . '/../includes/auth.php';
requireRole('lawyer');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Collect and sanitize input
$title       = trim($_POST['title'] ?? '');
$client_name = trim($_POST['client_name'] ?? '');
$case_type   = trim($_POST['case_type'] ?? '');
$status      = trim($_POST['status'] ?? 'pending');
$description = trim($_POST['description'] ?? '');
$lawyer_id   = $_SESSION['user_id'];

if (empty($title) || empty($client_name)) {
    echo json_encode(['success' => false, 'message' => 'Title and client name are required']);
    exit;
}

// Insert into `cases` table – columns must match DB schema (title, client_name, case_type, status, description, lawyer_id)
$conn = getDb();
$stmt = $conn->prepare("INSERT INTO cases (title, client_name, case_type, status, description, lawyer_id) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param('sssssi', $title, $client_name, $case_type, $status, $description, $lawyer_id);

if ($stmt->execute()) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        echo json_encode(['success' => true, 'message' => 'تم إنشاء القضية بنجاح']);
    } else {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
} else {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات: ' . $stmt->error]);
    } else {
        header("Location: " . $_SERVER['HTTP_REFERER']);
    }
}
?>
