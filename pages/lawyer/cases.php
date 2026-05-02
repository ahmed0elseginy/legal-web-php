<?php
require_once __DIR__ . "/../../includes/auth.php";
requireRole("lawyer");

$conn = getDb();
$user = getUser();
$pageTitle = "القضايا";
$currentPage = "cases";

$stmt = $conn->prepare("SELECT * FROM cases WHERE lawyer_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user["id"]);
$stmt->execute();
$cases = $stmt->get_result();

include __DIR__ . "/../../includes/header.php";
?>

<!-- Page Header -->
<div class="p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl lg:text-3xl font-bold text-dark-700">القضايا</h1>
        <p class="text-noble-600 mt-1">إدارة القضايا القانونية</p>
    </div>

    <!-- Create Case Button -->
    <div class="card p-6 mb-8">
        <h2 class="text-lg font-bold text-dark-700 mb-4">إنشاء قضية جديدة</h2>
        <form method="POST" action="<?= SITE_URL ?>/actions/create_case.php" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" name="title" placeholder="عنوان القضية" class="input-field" required>
                <input type="text" name="client_name" placeholder="اسم الموكل" class="input-field" required>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <input type="text" name="case_type" placeholder="نوع القضية" class="input-field">
                <select name="status" class="input-field">
                    <option value="pending">قيد الانتظار</option>
                    <option value="active">نشط</option>
                    <option value="completed">مكتمل</option>
                </select>
            </div>
            <textarea name="description" placeholder="وصف القضية" class="input-field min-h-[100px]"></textarea>
            <button type="submit" class="btn-primary flex items-center gap-2">
                <span class="iconify" data-icon="lucide:plus"></span>
                إنشاء قضية
            </button>
        </form>
    </div>

    <!-- Cases Table -->
    <div class="card overflow-hidden">
        <?php if ($cases->num_rows > 0): ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-noble-100 border-b border-noble-200">
                            <th class="text-right py-4 px-4 text-sm font-semibold text-dark-700">العنوان</th>
                            <th class="text-right py-4 px-4 text-sm font-semibold text-dark-700">الموكل</th>
                            <th class="text-right py-4 px-4 text-sm font-semibold text-dark-700">النوع</th>
                            <th class="text-right py-4 px-4 text-sm font-semibold text-dark-700">الحالة</th>
                            <th class="text-right py-4 px-4 text-sm font-semibold text-dark-700">المستند</th>
                            <th class="text-right py-4 px-4 text-sm font-semibold text-dark-700">التحكم</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($c = $cases->fetch_assoc()): ?>
                        <tr class="border-b border-noble-100 hover:bg-noble-50 transition-colors">
                            <td class="py-4 px-4">
                                <span class="font-semibold text-dark-700"><?= htmlspecialchars($c["title"]) ?></span>
                            </td>
                            <td class="py-4 px-4 text-noble-600"><?= htmlspecialchars($c["client_name"]) ?></td>
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
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2">
                                    <a href="edit_case.php?id=<?= $c['id'] ?>" class="text-blue-500 hover:text-blue-700" title="تعديل">
                                        <span class="iconify" data-icon="lucide:edit"></span>
                                    </a>
                                    <form method="POST" action="<?= SITE_URL ?>/actions/update_case_status.php" class="inline">
                                        <input type="hidden" name="case_id" value="<?= $c['id'] ?>">
                                        <select name="status" onchange="this.form.submit()" class="text-sm border border-noble-300 rounded-lg px-2 py-1">
                                            <option value="pending" <?= $c['status'] === 'pending' ? 'selected' : '' ?>>قيد الانتظار</option>
                                            <option value="active" <?= $c['status'] === 'active' ? 'selected' : '' ?>>نشط</option>
                                            <option value="completed" <?= $c['status'] === 'completed' ? 'selected' : '' ?>>مكتمل</option>
                                        </select>
                                    </form>
                                    <form method="POST" action="<?= SITE_URL ?>/actions/delete_case.php" class="inline" onsubmit="return confirm('هل أنت متأكد؟')">
                                        <input type="hidden" name="case_id" value="<?= $c['id'] ?>">
                                        <button type="submit" class="text-red-500 hover:text-red-700">
                                            <span class="iconify" data-icon="lucide:trash-2"></span>
                                        </button>
                                    </form>
                                </div>
                            </td>
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