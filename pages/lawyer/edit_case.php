<?php
require_once __DIR__ . "/../../includes/auth.php";
requireRole("lawyer");

$conn = getDb();
$user = getUser();
$case_id = $_GET["id"] ?? null;

if (!$case_id) {
    header("Location: cases.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM cases WHERE id = ? AND lawyer_id = ?");
$stmt->bind_param("ii", $case_id, $user["id"]);
$stmt->execute();
$case = $stmt->get_result()->fetch_assoc();

if (!$case) {
    header("Location: cases.php");
    exit;
}

$pageTitle = "تعديل القضية";
include __DIR__ . "/../../includes/header.php";
?>

<div class="p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl lg:text-3xl font-bold text-dark-700">تعديل القضية</h1>
        <p class="text-noble-600 mt-1">تعديل بيانات القضية: <?= htmlspecialchars($case['title']) ?></p>
    </div>

    <div class="card p-6 max-w-2xl">
        <form method="POST" action="<?= SITE_URL ?>/actions/update_case.php" class="space-y-4">
            <input type="hidden" name="case_id" value="<?= $case['id'] ?>">
            
            <div>
                <label class="block text-sm font-semibold text-dark-700 mb-1">عنوان القضية</label>
                <input type="text" name="title" value="<?= htmlspecialchars($case['title']) ?>" class="input-field" required>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-dark-700 mb-1">اسم الموكل</label>
                <input type="text" name="client_name" value="<?= htmlspecialchars($case['client_name']) ?>" class="input-field" required>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-dark-700 mb-1">نوع القضية</label>
                <input type="text" name="case_type" value="<?= htmlspecialchars($case['case_type']) ?>" class="input-field">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-dark-700 mb-1">وصف القضية</label>
                <textarea name="description" class="input-field min-h-[150px]"><?= htmlspecialchars($case['description']) ?></textarea>
            </div>
            
            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="btn-primary">حفظ التغييرات</button>
                <a href="cases.php" class="text-noble-600 hover:text-dark-700">إلغاء</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>
