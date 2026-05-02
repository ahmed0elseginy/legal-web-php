<?php
require_once __DIR__ . "/../includes/auth.php";
requireRole("lawyer");

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $case_id = $_POST["case_id"] ?? null;
    $lawyer_id = $_SESSION["user_id"];

    if (!$case_id) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo json_encode(['success' => false, 'message' => 'ID missing']);
        } else {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        }
        exit;
    }

    $conn = getDb();
    $stmt = $conn->prepare("DELETE FROM cases WHERE id = ? AND lawyer_id = ?");
    $stmt->bind_param("ii", $case_id, $lawyer_id);

    if ($stmt->execute()) {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo json_encode(['success' => true, 'message' => 'تم حذف القضية بنجاح']);
        } else {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        }
    } else {
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            echo json_encode(['success' => false, 'message' => 'خطأ في قاعدة البيانات']);
        } else {
            header("Location: " . $_SERVER['HTTP_REFERER']);
        }
    }
} else {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    } else {
        header("Location: " . SITE_URL);
    }
}
