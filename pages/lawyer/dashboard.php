<?php
require_once __DIR__ . "/../../includes/auth.php";
requireRole("lawyer");

$conn = getDb();
$user = getUser();
$pageTitle = "لوحة المعلومات";
$currentPage = "dashboard";

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM cases WHERE lawyer_id = ?");
$stmt->bind_param("i", $user["id"]);
$stmt->execute();
$casesCount = $stmt->get_result()->fetch_assoc()["total"];

$stmt = $conn->prepare("SELECT COUNT(DISTINCT client_id) as total FROM cases WHERE lawyer_id = ?");
$stmt->bind_param("i", $user["id"]);
$stmt->execute();
$clientsCount = $stmt->get_result()->fetch_assoc()["total"];

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM service_requests WHERE lawyer_id = ? AND status = 'pending'");
$stmt->bind_param("i", $user["id"]);
$stmt->execute();
$pendingRequests = $stmt->get_result()->fetch_assoc()["total"];

$stmt = $conn->prepare("SELECT id, title, client_name, status, created_at FROM cases WHERE lawyer_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $user["id"]);
$stmt->execute();
$recentCases = $stmt->get_result();

$statusDist = [];
$stmt = $conn->prepare("SELECT status, COUNT(*) as count FROM cases WHERE lawyer_id = ? GROUP BY status");
$stmt->bind_param("i", $user["id"]);
$stmt->execute();
$distResult = $stmt->get_result();
while ($row = $distResult->fetch_assoc()) {
    $statusDist[$row['status']] = $row['count'];
}

include __DIR__ . "/../../includes/header.php";
?>

<!-- Page Header -->
<div class="p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl lg:text-3xl font-bold text-dark-700">مرحباً، <?= htmlspecialchars($user["name"]) ?></h1>
        <p class="text-noble-600 mt-1">إليك ملخص نشاطك القانوني</p>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-8">
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
                <span class="iconify text-2xl text-justice-600" data-icon="lucide:users"></span>
            </div>
            <div>
                <p class="text-3xl font-bold text-dark-700"><?= $clientsCount ?></p>
                <p class="text-sm text-noble-600">الموكلين</p>
            </div>
        </div>
        <div class="card p-5 flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gold-100 flex items-center justify-center">
                <span class="iconify text-2xl text-gold-600" data-icon="lucide:file-text"></span>
            </div>
            <div>
                <p class="text-3xl font-bold text-dark-700"><?= $pendingRequests ?></p>
                <p class="text-sm text-noble-600">الطلبات المعلقة</p>
            </div>
        </div>
    </div>

    <!-- Status Distribution -->
    <?php if (count($statusDist) > 0): ?>
    <div class="card p-5 mb-8">
        <h3 class="text-lg font-bold text-dark-700 mb-4">توزيع القضايا حسب الحالة</h3>
        <div class="flex flex-wrap gap-4">
            <?php foreach ($statusDist as $status => $count): ?>
                <?php 
                $statusClasses = [
                    'pending' => 'bg-amber-100 text-amber-700',
                    'active' => 'bg-blue-100 text-blue-700',
                    'completed' => 'bg-purple-100 text-purple-700'
                ];
                $statusLabels = [
                    'pending' => 'قيد الانتظار',
                    'active' => 'نشط',
                    'completed' => 'مكتمل'
                ];
                $class = $statusClasses[$status] ?? 'bg-gray-100 text-gray-700';
                $label = $statusLabels[$status] ?? $status;
                ?>
                <div class="flex items-center gap-2 px-4 py-2 rounded-xl <?= $class ?>">
                    <span class="font-bold"><?= $count ?></span>
                    <span class="text-sm"><?= $label ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recent Cases -->
    <div class="card p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-lg font-bold text-dark-700">القضايا الأخيرة</h2>
            <a href="<?= SITE_URL ?>/pages/lawyer/cases.php" class="text-sm text-gold-600 hover:text-gold-700 flex items-center gap-1">
                عرض الكل <span class="iconify" data-icon="lucide:arrow-left"></span>
            </a>
        </div>
        
        <?php if ($recentCases->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-noble-200">
                            <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">العنوان</th>
                            <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">الموكل</th>
                            <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">الحالة</th>
                            <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($c = $recentCases->fetch_assoc()): ?>
                        <tr class="border-b border-noble-100 hover:bg-noble-50 transition-colors">
                            <td class="py-4 px-2 font-medium text-dark-700"><?= htmlspecialchars($c["title"]) ?></td>
                            <td class="py-4 px-2 text-noble-600"><?= htmlspecialchars($c["client_name"]) ?></td>
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
                <div class="w-16 h-16 rounded-full bg-noble-200 flex items-center justify-center mx-auto mb-4">
                    <span class="iconify text-2xl text-noble-400" data-icon="lucide:briefcase"></span>
                </div>
                <p class="text-noble-600">لا توجد قضايا بعد</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>