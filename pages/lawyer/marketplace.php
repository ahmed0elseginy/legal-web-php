<?php
require_once __DIR__ . "/../../includes/auth.php";
requireRole("lawyer");

$conn = getDb();
$user = getUser();
$pageTitle = "السوق";
$currentPage = "marketplace";

$stmt = $conn->prepare("SELECT id, name, description, price, status FROM services WHERE lawyer_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user["id"]);
$stmt->execute();
$myServices = $stmt->get_result();

$stmt = $conn->prepare("SELECT sr.*, u.name as client_name, s.name as service_name FROM service_requests sr JOIN users u ON sr.client_id = u.id JOIN services s ON sr.service_id = s.id WHERE sr.lawyer_id = ? ORDER BY sr.created_at DESC");
$stmt->bind_param("i", $user["id"]);
$stmt->execute();
$serviceRequests = $stmt->get_result();

include __DIR__ . "/../../includes/header.php";
?>

<!-- Page Header -->
<div class="p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl lg:text-3xl font-bold text-dark-700">إدارة الخدمات القانونية</h1>
        <p class="text-noble-600 mt-1">إدارة ا    <!-- Tabs Navigation -->
    <div class="flex items-center gap-1 mb-8 bg-white p-1 rounded-2xl w-fit shadow-sm border border-noble-200">
        <button onclick="switchTab('services')" id="tab-services" class="px-6 py-2.5 rounded-xl font-semibold transition-all duration-300 active-tab">
            إدارة خدماتي
        </button>
        <button onclick="switchTab('requests')" id="tab-requests" class="px-6 py-2.5 rounded-xl font-semibold transition-all duration-300 text-noble-600 hover:bg-noble-50">
            طلبات العملاء (<?= $serviceRequests->num_rows ?>)
        </button>
    </div>

    <!-- Tab 1: My Services -->
    <div id="content-services" class="tab-content">
        <!-- Create Service -->
        <div class="card p-6 mb-8">
            <h2 class="text-lg font-bold text-dark-700 mb-4">إضافة خدمة جديدة</h2>
            <form method="POST" action="<?= SITE_URL ?>/actions/create_service.php" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" name="name" placeholder="اسم الخدمة" class="input-field" required>
                    <input type="number" name="price" placeholder="السعر (ج.م)" class="input-field" required>
                </div>
                <textarea name="description" placeholder="وصف الخدمة" class="input-field min-h-[100px]" required></textarea>
                <button type="submit" class="btn-primary flex items-center gap-2">
                    <span class="iconify" data-icon="lucide:plus"></span>
                    إضافة خدمة
                </button>
            </form>
        </div>

        <!-- My Services List -->
        <div class="card p-6">
            <h2 class="text-lg font-bold text-dark-700 mb-6">قائمة خدماتي</h2>
            <?php if ($myServices->num_rows > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-noble-200">
                                <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">الخدمة</th>
                                <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">السعر</th>
                                <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">الحالة</th>
                                <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">التحكم</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($s = $myServices->fetch_assoc()): ?>
                            <tr class="border-b border-noble-100 hover:bg-noble-50 transition-colors">
                                <td class="py-4 px-2 font-medium text-dark-700"><?= htmlspecialchars($s["name"]) ?></td>
                                <td class="py-4 px-2 text-gold-600 font-semibold"><?= number_format($s["price"]) ?> ج.م</td>
                                <td class="py-4 px-2">
                                    <?php 
                                    $statusClass = $s['status'] === 'published' ? 'badge-accepted' : 'badge-pending';
                                    $statusLabel = $s['status'] === 'published' ? 'منشور' : 'مسودة';
                                    ?>
                                    <span class="badge <?= $statusClass ?>"><?= $statusLabel ?></span>
                                </td>
                                <td class="py-4 px-2">
                                    <div class="flex items-center gap-2">
                                        <a href="edit_service.php?id=<?= $s['id'] ?>" class="text-blue-500 hover:text-blue-700" title="تعديل">
                                            <span class="iconify" data-icon="lucide:edit"></span>
                                        </a>
                                        <form method="POST" action="<?= SITE_URL ?>/actions/toggle_service.php" class="inline">
                                            <input type="hidden" name="service_id" value="<?= $s['id'] ?>">
                                            <button type="submit" class="text-noble-600 hover:text-gold-600" title="<?= $s['status'] === 'published' ? 'إخفاء' : 'نشر' ?>">
                                                <span class="iconify" data-icon="<?= $s['status'] === 'published' ? 'lucide:eye-off' : 'lucide:eye' ?>"></span>
                                            </button>
                                        </form>
                                        <form method="POST" action="<?= SITE_URL ?>/actions/delete_service.php" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخدمة؟')">
                                            <input type="hidden" name="service_id" value="<?= $s['id'] ?>">
                                            <button type="submit" class="text-red-500 hover:text-red-700" title="حذف">
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
                <div class="text-center py-8">
                    <p class="text-noble-500">لم تقم بإضافة أي خدمات بعد</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab 2: Service Requests -->
    <div id="content-requests" class="tab-content hidden">
        <div class="card p-6">
            <h2 class="text-lg font-bold text-dark-700 mb-6">طلبات العملاء الواردة</h2>
            <?php if ($serviceRequests->num_rows > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-noble-200">
                                <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">الخدمة</th>
                                <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">العميل</th>
                                <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">الحالة</th>
                                <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">التحكم</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($r = $serviceRequests->fetch_assoc()): ?>
                            <tr class="border-b border-noble-100 hover:bg-noble-50 transition-colors">
                                <td class="py-4 px-2 font-medium text-dark-700"><?= htmlspecialchars($r["service_name"]) ?></td>
                                <td class="py-4 px-2 text-noble-600"><?= htmlspecialchars($r["client_name"]) ?></td>
                                <td class="py-4 px-2">
                                    <?php 
                                    $statusClasses = [
                                        'pending' => 'badge-pending',
                                        'accepted' => 'badge-accepted',
                                        'rejected' => 'badge-rejected'
                                    ];
                                    $statusLabels = [
                                        'pending' => 'قيد الانتظار',
                                        'accepted' => 'مقبول',
                                        'rejected' => 'مرفوض'
                                    ];
                                    $class = $statusClasses[$r['status']] ?? 'badge-pending';
                                    $label = $statusLabels[$r['status']] ?? $r['status'];
                                    ?>
                                    <span class="badge <?= $class ?>"><?= $label ?></span>
                                </td>
                                <td class="py-4 px-2">
                                    <div class="flex items-center gap-3">
                                        <button 
                                            data-desc="<?= htmlspecialchars($r['description'] ?? 'لا توجد تفاصيل إضافية') ?>" 
                                            data-client="<?= htmlspecialchars($r['client_name']) ?>"
                                            onclick="showDetails(this)" 
                                            class="text-gold-600 hover:text-gold-700" 
                                            title="عرض التفاصيل">
                                            <span class="iconify" data-icon="lucide:eye"></span>
                                        </button>
                                        
                                        <?php if ($r['status'] === 'accepted'): ?>
                                            <a href="<?= SITE_URL ?>/pages/lawyer/chat.php?client_id=<?= $r['client_id'] ?>" class="text-blue-600 hover:text-blue-800" title="محادثة العميل">
                                                <span class="iconify" data-icon="lucide:message-circle"></span>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($r['status'] === 'pending'): ?>
                                        <div class="flex items-center gap-2">
                                            <form method="POST" action="<?= SITE_URL ?>/actions/handle_request.php" class="inline">
                                                <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                                <input type="hidden" name="action" value="accept">
                                                <button type="submit" class="text-green-600 hover:text-green-700" title="قبول">
                                                    <span class="iconify" data-icon="lucide:check"></span>
                                                </button>
                                            </form>
                                            <form method="POST" action="<?= SITE_URL ?>/actions/handle_request.php" class="inline">
                                                <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="text-red-600 hover:text-red-700" title="رفض">
                                                    <span class="iconify" data-icon="lucide:x"></span>
                                                </button>
                                            </form>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <div class="mb-4">
                        <img src="<?= SITE_URL ?>/assets/images/networking.png" alt="Networking" class="w-32 mx-auto opacity-50 grayscale">
                    </div>
                    <p class="text-noble-600">لا توجد طلبات جديدة حالياً</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Details Modal -->
<div id="detailsModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-dark-900/50 backdrop-blur-sm p-4">
    <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden animate-fade-in-up">
        <div class="p-6 border-b border-noble-200 flex items-center justify-between bg-noble-50">
            <h3 class="text-xl font-bold text-dark-700">تفاصيل طلب العميل: <span id="modalClientName" class="text-gold-600"></span></h3>
            <button onclick="hideDetails()" class="text-noble-500 hover:text-dark-700">
                <span class="iconify text-2xl" data-icon="lucide:x"></span>
            </button>
        </div>
        <div class="p-6">
            <p id="modalDescription" class="text-noble-700 leading-relaxed whitespace-pre-wrap"></p>
        </div>
        <div class="p-4 bg-noble-50 border-t border-noble-100 flex justify-end">
            <button onclick="hideDetails()" class="btn-primary">إغلاق</button>
        </div>
    </div>
</div>

<style>
    .active-tab {
        background: linear-gradient(135deg, #C5A55A 0%, #B8944D 100%);
        color: #1F1B18;
        box-shadow: 0 4px 12px rgba(197, 165, 90, 0.2);
    }
</style>

<script>
function switchTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    document.getElementById('content-' + tabId).classList.remove('hidden');
    
    document.getElementById('tab-services').classList.remove('active-tab');
    document.getElementById('tab-services').classList.add('text-noble-600', 'hover:bg-noble-50');
    document.getElementById('tab-requests').classList.remove('active-tab');
    document.getElementById('tab-requests').classList.add('text-noble-600', 'hover:bg-noble-50');
    
    const activeBtn = document.getElementById('tab-' + tabId);
    activeBtn.classList.add('active-tab');
    activeBtn.classList.remove('text-noble-600', 'hover:bg-noble-50');
}

function showDetails(btn) {
    const desc = btn.getAttribute('data-desc');
    const name = btn.getAttribute('data-client');
    
    document.getElementById('modalDescription').innerText = desc || "لا توجد تفاصيل إضافية";
    document.getElementById('modalClientName').innerText = name;
    document.getElementById('detailsModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function hideDetails() {
    document.getElementById('detailsModal').classList.add('hidden');
}
</script>

<?php include __DIR__ . "/../../includes/footer.php"; ?>