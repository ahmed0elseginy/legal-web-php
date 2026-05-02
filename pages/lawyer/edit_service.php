<?php
require_once __DIR__ . "/../../includes/auth.php";
requireRole("lawyer");

$conn = getDb();
$user = getUser();
$service_id = $_GET["id"] ?? null;

if (!$service_id) {
    header("Location: marketplace.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM services WHERE id = ? AND lawyer_id = ?");
$stmt->bind_param("ii", $service_id, $user["id"]);
$stmt->execute();
$service = $stmt->get_result()->fetch_assoc();

if (!$service) {
    header("Location: marketplace.php");
    exit;
}

$pageTitle = "تعديل الخدمة";
include __DIR__ . "/../../includes/header.php";
?>

<div class="p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl lg:text-3xl font-bold text-dark-700">تعديل الخدمة</h1>
        <p class="text-noble-600 mt-1">تعديل بيانات الخدمة: <?= htmlspecialchars($service['name']) ?></p>
    </div>

    <div class="card p-6 max-w-2xl">
        <form method="POST" action="<?= SITE_URL ?>/actions/update_service.php" class="space-y-4">
            <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
            
            <div>
                <label class="block text-sm font-semibold text-dark-700 mb-1">اسم الخدمة</label>
                <input type="text" name="name" value="<?= htmlspecialchars($service['name']) ?>" class="input-field" required>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-dark-700 mb-1">السعر (ج.م)</label>
                <input type="number" name="price" value="<?= $service['price'] ?>" class="input-field" required>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-dark-700 mb-1">وصف الخدمة</label>
                <textarea name="description" class="input-field min-h-[150px]" required><?= htmlspecialchars($service['description']) ?></textarea>
            </div>
            
            <div class="flex items-center gap-4 pt-4">
                <button type="submit" class="btn-primary">حفظ التغييرات</button>
                <a href="marketplace.php" class="text-noble-600 hover:text-dark-700">إلغاء</a>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>
