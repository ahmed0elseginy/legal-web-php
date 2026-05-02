<?php
require_once __DIR__ . "/../includes/auth.php";
requireAuth();
$role = $_SESSION["user_role"];
$user_id = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $case_id = $_POST["case_id"] ?? null;

    if (!$case_id || !isset($_FILES["document"])) {
        setFlash("بيانات غير مكتملة", "error");
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    $file = $_FILES["document"];
    $fileName = $file["name"];
    $fileTmpName = $file["tmp_name"];
    $fileSize = $file["size"];
    $fileError = $file["error"];

    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowed = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];

    if (in_array($fileExt, $allowed)) {
        if ($fileError === 0) {
            if ($fileSize < 5000000) { // 5MB limit
                $fileNameNew = "case_" . $case_id . "_" . uniqid('', true) . "." . $fileExt;
                $uploadDir = __DIR__ . "/../assets/uploads/";
                
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileDestination = $uploadDir . $fileNameNew;
                
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    $conn = getDb();
                    $doc_url = "/assets/uploads/" . $fileNameNew;
                    
                    if ($role === 'lawyer') {
                        $stmt = $conn->prepare("UPDATE cases SET document_url = ? WHERE id = ? AND lawyer_id = ?");
                    } else {
                        $stmt = $conn->prepare("UPDATE cases SET document_url = ? WHERE id = ? AND client_id = ?");
                    }
                    $stmt->bind_param("sii", $doc_url, $case_id, $user_id);
                    
                    if ($stmt->execute()) {
                        setFlash("تم رفع الملف بنجاح");
                    } else {
                        setFlash("خطأ في تحديث قاعدة البيانات", "error");
                    }
                } else {
                    setFlash("فشل في نقل الملف إلى المجلد المخصص", "error");
                }
            } else {
                setFlash("حجم الملف كبير جداً (الحد الأقصى 5 ميجابايت)", "error");
            }
        } else {
            setFlash("حدث خطأ أثناء رفع الملف", "error");
        }
    } else {
        setFlash("نوع الملف غير مسموح به (PDF, Word, Images فقط)", "error");
    }
}

header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
