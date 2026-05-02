<?php
require_once __DIR__ . "/../../includes/auth.php";
requireRole("client");

$conn = getDb();
$user = getUser();
$pageTitle = "المواعيد";
$currentPage = "appointments";

$stmt = $conn->prepare("SELECT a.*, u.name as lawyer_name FROM appointments a LEFT JOIN users u ON a.lawyer_id = u.id WHERE a.client_id = ? ORDER BY a.appointment_date DESC, a.appointment_time DESC");
$stmt->bind_param("i", $user["id"]);
$stmt->execute();
$appointments = $stmt->get_result();

include __DIR__ . "/../../includes/header.php";
?>

<!-- Page Header -->
<div class="p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl lg:text-3xl font-bold text-dark-700">المواعيد</h1>
        <p class="text-noble-600 mt-1">متابعة مواعيدك مع المحامين</p>
    </div>

    <!-- Appointments Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if ($appointments->num_rows > 0): ?>
            <?php while ($a = $appointments->fetch_assoc()): ?>
            <div class="card p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-12 h-12 rounded-xl bg-justice-100 flex items-center justify-center">
                        <span class="iconify text-xl text-justice-600" data-icon="lucide:calendar"></span>
                    </div>
                    <?php 
                    $statusClasses = [
                        'pending' => 'badge-pending',
                        'confirmed' => 'badge-accepted',
                        'completed' => 'badge-completed',
                        'cancelled' => 'badge-rejected'
                    ];
                    $statusLabels = [
                        'pending' => 'قيد الانتظار',
                        'confirmed' => 'مؤكد',
                        'completed' => 'مكتمل',
                        'cancelled' => 'ملغى'
                    ];
                    $class = $statusClasses[$a['status']] ?? 'badge-pending';
                    $label = $statusLabels[$a['status']] ?? $a['status'];
                    ?>
                    <span class="badge <?= $class ?>"><?= $label ?></span>
                </div>
                <h3 class="font-bold text-dark-700 mb-2"><?= htmlspecialchars($a["title"]) ?></h3>
                <p class="text-sm text-noble-600 mb-3"><?= htmlspecialchars($a["lawyer_name"] ?? "-") ?></p>
                <div class="flex items-center gap-4 text-sm text-noble-500">
                    <span class="flex items-center gap-1">
                        <span class="iconify" data-icon="lucide:calendar"></span>
                        <?= $a["appointment_date"] ?>
                    </span>
                    <span class="flex items-center gap-1">
                        <span class="iconify" data-icon="lucide:clock"></span>
                        <?= $a["appointment_time"] ?? "--:--" ?>
                    </span>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
        <div class="col-span-full text-center py-16">
            <div class="w-20 h-20 rounded-full bg-noble-200 flex items-center justify-center mx-auto mb-4">
                <span class="iconify text-3xl text-noble-400" data-icon="lucide:calendar"></span>
            </div>
            <p class="text-noble-600 text-lg">لا توجد مواعيد</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>