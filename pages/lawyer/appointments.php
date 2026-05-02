<?php
require_once __DIR__ . "/../../includes/auth.php";
requireRole("lawyer");

$conn = getDb();
$user = getUser();
$pageTitle = "المواعيد";
$currentPage = "appointments";

$stmt = $conn->prepare("SELECT a.*, u.name as client_name FROM appointments a LEFT JOIN users u ON a.client_id = u.id WHERE a.lawyer_id = ? ORDER BY a.appointment_date ASC");
$stmt->bind_param("i", $user["id"]);
$stmt->execute();
$appointments = $stmt->get_result();

include __DIR__ . "/../../includes/header.php";
?>

<!-- Page Header -->
<div class="p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl lg:text-3xl font-bold text-dark-700">المواعيد</h1>
        <p class="text-noble-600 mt-1">إدارة المواعيد القادمة</p>
    </div>

    <!-- Appointments Table -->
    <div class="card overflow-hidden">
        <?php if ($appointments->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-noble-100 border-b border-noble-200">
                            <th class="text-right py-4 px-4 text-sm font-semibold text-dark-700">العنوان</th>
                            <th class="text-right py-4 px-4 text-sm font-semibold text-dark-700">الموكل</th>
                            <th class="text-right py-4 px-4 text-sm font-semibold text-dark-700">التاريخ</th>
                            <th class="text-right py-4 px-4 text-sm font-semibold text-dark-700">الوقت</th>
                            <th class="text-right py-4 px-4 text-sm font-semibold text-dark-700">الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($a = $appointments->fetch_assoc()): ?>
                        <tr class="border-b border-noble-100 hover:bg-noble-50 transition-colors">
                            <td class="py-4 px-4">
                                <span class="font-semibold text-dark-700"><?= htmlspecialchars($a["title"]) ?></span>
                            </td>
                            <td class="py-4 px-4 text-noble-600"><?= htmlspecialchars($a["client_name"] ?? "-") ?></td>
                            <td class="py-4 px-4 text-noble-600"><?= $a["appointment_date"] ?></td>
                            <td class="py-4 px-4 text-noble-600"><?= $a["appointment_time"] ?? "--:--" ?></td>
                            <td class="py-4 px-4">
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
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-16">
                <div class="w-20 h-20 rounded-full bg-noble-200 flex items-center justify-center mx-auto mb-4">
                    <span class="iconify text-3xl text-noble-400" data-icon="lucide:calendar"></span>
                </div>
                <p class="text-noble-600 text-lg">لا توجد مواعيد</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>