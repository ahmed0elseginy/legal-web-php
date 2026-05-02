<?php
require_once __DIR__ . "/../../includes/auth.php";
requireRole("client");

$conn = getDb();
$user = getUser();
$pageTitle = "قضاياي";
$currentPage = "cases";

$stmt = $conn->prepare("SELECT * FROM cases WHERE client_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user["id"]);
$stmt->execute();
$cases = $stmt->get_result();

include __DIR__ . "/../../includes/header.php";
?>

<!-- Page Header -->
<div class="p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl lg:text-3xl font-bold text-dark-700">قضاياي</h1>
        <p class="text-noble-600 mt-1">متابعة جميع القضايا القانونية</p>
    </div>

    <!-- Cases Table -->
    <div class="card overflow-hidden">
        <?php if ($cases->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-noble-100 border-b border-noble-200">
                            <th class="text-right py-4 px-4 text-sm font-semibold text-dark-700">العنوان</th>
                            <th class="text-right py-4 px-4 text-sm font-semibold text-dark-700">المحامي</th>
                            <th class="text-right py-4 px-4 text-sm font-semibold text-dark-700">النوع</th>
                            <th class="text-right py-4 px-4 text-sm font-semibold text-dark-700">الحالة</th>
                            <th class="text-right py-4 px-4 text-sm font-semibold text-dark-700">المستند</th>
                            <th class="text-right py-4 px-4 text-sm font-semibold text-dark-700">التاريخ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($c = $cases->fetch_assoc()): ?>
                        <tr class="border-b border-noble-100 hover:bg-noble-50 transition-colors">
                            <td class="py-4 px-4">
                                <span class="font-semibold text-dark-700"><?= htmlspecialchars($c["title"]) ?></span>
                            </td>
                            <td class="py-4 px-4 text-noble-600"><?= htmlspecialchars($c["lawyer_name"] ?? "-") ?></td>
                            <td class="py-4 px-4 text-noble-600"><?= htmlspecialchars($c["case_type"] ?? "-") ?></td>
                            <td class="py-4 px-4">
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
                            <td class="py-4 px-4">
                                <?php if (!empty($c["document_url"])): ?>
                                    <a href="<?= SITE_URL . $c['document_url'] ?>" target="_blank" class="inline-flex items-center gap-1 text-gold-600 hover:text-gold-700">
                                        <span class="iconify text-sm" data-icon="lucide:eye"></span>
                                        عرض
                                    </a>
                                <?php else: ?>
                                    <form action="<?= SITE_URL ?>/actions/upload_case_doc.php" method="POST" enctype="multipart/form-data" class="flex items-center gap-2">
                                        <input type="hidden" name="case_id" value="<?= $c['id'] ?>">
                                        <input type="file" name="document" class="text-sm file:hidden w-24" required>
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-justice-500 text-white text-sm hover:bg-justice-600">
                                            رفع
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-4 text-noble-500 text-sm"><?= $c["created_at"] ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-16">
                <div class="w-20 h-20 rounded-full bg-noble-200 flex items-center justify-center mx-auto mb-4">
                    <span class="iconify text-3xl text-noble-400" data-icon="lucide:briefcase"></span>
                </div>
                <p class="text-noble-600 text-lg mb-4">لا توجد قضايا</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>