<?php
require_once __DIR__ . "/../includes/auth.php";

header('Content-Type: application/json');

$email = trim($_GET['email'] ?? '');

if (empty($email)) {
    echo json_encode(['available' => true]);
    exit;
}

$conn = getDb();
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo json_encode(['available' => false]);
} else {
    echo json_encode(['available' => true]);
}
