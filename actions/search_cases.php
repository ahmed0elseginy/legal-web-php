<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole("lawyer");

header('Content-Type: application/json');

$query = trim($_GET['q'] ?? '');
$user_id = $_SESSION["user_id"];

if (empty($query)) {
    echo json_encode([]);
    exit;
}

$conn = getDb();
$search = "%$query%";
$stmt = $conn->prepare("SELECT id, title, client_name, status FROM cases WHERE lawyer_id = ? AND (title LIKE ? OR client_name LIKE ?) LIMIT 5");
$stmt->bind_param("iss", $user_id, $search, $search);
$stmt->execute();
$result = $stmt->get_result();

$cases = [];
while ($row = $result->fetch_assoc()) {
    $cases[] = $row;
}

echo json_encode($cases);
