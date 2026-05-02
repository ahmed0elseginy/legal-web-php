<?php
require_once __DIR__ . "/../../includes/auth.php";
requireRole("client");

$conn = getDb();
$user = getUser();
$pageTitle = "لوحة المعلومات";
$currentPage = "dashboard";

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM cases WHERE client_id = ?");
$stmt->bind_param("i", $user["id"]);
$stmt->execute();
$casesCount = $stmt->get_result()->fetch_assoc()["total"];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM service_requests WHERE client_id = ?");
$stmt->bind_param("i", $user["id"]);
$stmt->execute();
$requestsCount = $stmt->get_result()->fetch_assoc()["total"];

$stmt = $conn->prepare("SELECT id, title, lawyer_name, status, created_at FROM cases WHERE client_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $user["id"]);
$stmt->execute();
$recentCases = $stmt->get_result();

include __DIR__ . "/../../includes/header.php";
?>

<!-- Page Header -->
<div class="p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl lg:text-3xl font-bold text-dark-700">مرحباً، <?= htmlspecialchars($user["name"]) ?></h1>
        <p class="text-noble-600 mt-1">هذه هي نظرة عامة على حسابك القانوني</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-8">
        <div class="card p-5 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-justice-100 flex items-center justify-center">
                <span class="iconify text-2xl text-justice-600" data-icon="lucide:briefcase"></span>
            </div>
            <div>
                <p class="text-3xl font-bold text-dark-700"><?= $casesCount ?></p>
                <p class="text-sm text-noble-600">القضايا</p>
            </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-justice-100 flex items-center justify-center">
                <span class="iconify text-2xl text-justice-600" data-icon="lucide:file-text"></span>
            </div>
            <div>
                <p class="text-3xl font-bold text-dark-700"><?= $requestsCount ?></p>
                <p class="text-sm text-noble-600">الطلبات</p>
            </div>
        </div>
    </div>

    <!-- Recent Cases -->
    <div class="card p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-dark-700">القضايا الأخيرة</h2>
            <a href="<?= SITE_URL ?>/pages/client/cases.php" class="text-sm text-gold-600 hover:text-gold-700 flex items-center gap-1">
                عرض الكل <span class="iconify" data-icon="lucide:arrow-left"></span>
            </a>
        </div>
        
        <?php if ($recentCases->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-noble-200">
                            <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">العنوان</th>
                            <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">المحامي</th>
                            <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">الحالة</th>
                            <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($c = $recentCases->fetch_assoc()): ?>
                        <tr class="border-b border-noble-100 hover:bg-noble-50 transition-colors">
                            <td class="py-4 px-2 font-medium text-dark-700"><?= htmlspecialchars($c["title"]) ?></td>
                            <td class="py-4 px-2 text-noble-600"><?= htmlspecialchars($c["lawyer_name"] ?? "-") ?></td>
                            <td class="py-4 px-2">
                                <?php 
                                $statusClasses = [
                                    'pending' => 'badge-pending',
                                    'active' => 'badge-active',
                                    'completed' => 'badge-completed'
                                ];
                                $statusLabels = [
                                    'pending' => 'قيد الانتظار',
                                    'active' => 'نشط',
                                    'completed' => 'مكتمل'
                                ];
                                $class = $statusClasses[$c['status']] ?? 'badge-pending';
                                $label = $statusLabels[$c['status']] ?? $c['status'];
                                ?>
                                <span class="badge <?= $class ?>"><?= $label ?></span>
                            </td>
                            <td class="py-4 px-2 text-noble-600 text-sm"><?= $c["created_at"] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-12">
                <div class="mb-4">
                    <img src="<?= SITE_URL ?>/assets/images/document.png" alt="Documents" class="w-24 mx-auto opacity-50 grayscale">
                </div>
                <p class="text-noble-600">لا توجد قضايا بعد</p>
                <a href="<?= SITE_URL ?>/pages/client/cases.php" class="btn-primary inline-block mt-4">إنشاء قضية جديدة</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>