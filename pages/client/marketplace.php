<?php
require_once __DIR__ . "/../../includes/auth.php";
requireRole("client");

$conn = getDb();
$user = getUser();
$pageTitle = "سوق الخدمات";
$currentPage = "marketplace";

$services = $conn->query("SELECT s.*, u.name as lawyer_name FROM services s JOIN users u ON s.lawyer_id = u.id WHERE s.status = 'published' ORDER BY s.created_at DESC");

$stmt = $conn->prepare("SELECT sr.*, s.name as service_name, u.name as lawyer_name FROM service_requests sr JOIN services s ON sr.service_id = s.id JOIN users u ON sr.lawyer_id = u.id WHERE sr.client_id = ? ORDER BY sr.created_at DESC");
$stmt->bind_param("i", $user["id"]);
$stmt->execute();
$myRequests = $stmt->get_result();

$lawyers = $conn->query("SELECT id, name, email FROM users WHERE role = 'lawyer' ORDER BY name ASC");

include __DIR__ . "/../../includes/header.php";
?>

<!-- Page Header -->
<div class="p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl lg:text-3xl font-bold text-dark-700">الخدمات القانونية</h1>
        <p class="text-noble-600 mt-1">تصفح واختر الخدمات القانونية المتاحة من المحامين</p>
    </div>

    <!-- Tabs Navigation -->
    <div class="flex items-center gap-1 mb-8 bg-white p-1 rounded-2xl w-fit shadow-sm border border-noble-200">
        <button onclick="switchTab('available')" id="tab-available" class="px-6 py-2.5 rounded-xl font-semibold transition-all duration-300 active-tab">
            الخدمات المتاحة
        </button>
        <button onclick="switchTab('lawyers')" id="tab-lawyers" class="px-6 py-2.5 rounded-xl font-semibold transition-all duration-300 text-noble-600 hover:bg-noble-50">
            المحامون
        </button>
        <button onclick="switchTab('my-requests')" id="tab-my-requests" class="px-6 py-2.5 rounded-xl font-semibold transition-all duration-300 text-noble-600 hover:bg-noble-50">
            طلباتي (<?= $myRequests->num_rows ?>)
        </button>
    </div>

    <!-- Tab 1: Available Services -->
    <div id="content-available" class="tab-content">
        <!-- Services Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            <?php if ($services->num_rows > 0): ?>
                <?php while ($s = $services->fetch_assoc()): ?>
                <div class="card overflow-hidden">
                    <div class="h-32 bg-gradient-to-br from-justice-500 to-justice-700 flex items-center justify-center">
                        <span class="iconify text-5xl text-white/30" data-icon="lucide:scale"></span>
                    </div>
                    <div class="p-5">
                        <h3 class="text-lg font-bold text-dark-700 mb-2"><?= htmlspecialchars($s["name"]) ?></h3>
                        <p class="text-noble-600 text-sm mb-3 line-clamp-2"><?= htmlspecialchars($s["description"]) ?></p>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm text-noble-500">المحامي: <?= htmlspecialchars($s["lawyer_name"]) ?></span>
                            <span class="text-lg font-bold text-gold-600"><?= number_format($s["price"]) ?> ج.م</span>
                        </div>
                        <button 
                            data-id="<?= $s['id'] ?>" 
                            data-lawyer="<?= $s['lawyer_id'] ?>" 
                            data-name="<?= htmlspecialchars($s['name']) ?>"
                            onclick="openRequestModal(this)" 
                            class="btn-primary w-full flex items-center justify-center gap-2">
                            <span class="iconify" data-icon="lucide:send"></span>
                            طلب الخدمة
                        </button>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
            <div class="col-span-full text-center py-12">
                <div class="w-20 h-20 rounded-full bg-noble-200 flex items-center justify-center mx-auto mb-4">
                    <span class="iconify text-3xl text-noble-400" data-icon="lucide:store"></span>
                </div>
                <p class="text-noble-600 text-lg">لا توجد خدمات متاحة حالياً</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab 2: Lawyers -->
    <div id="content-lawyers" class="tab-content hidden">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
            <?php if ($lawyers->num_rows > 0): ?>
                <?php while ($l = $lawyers->fetch_assoc()): ?>
                <div class="card p-6 flex flex-col items-center text-center">
                    <div class="w-20 h-20 rounded-full bg-justice-100 flex items-center justify-center mb-4 text-justice-600 font-bold text-2xl">
                        <?= strtoupper(substr($l["name"], 0, 1)) ?>
                    </div>
                    <h3 class="text-lg font-bold text-dark-700"><?= htmlspecialchars($l["name"]) ?></h3>
                    <p class="text-noble-500 text-sm mb-6"><?= htmlspecialchars($l["email"]) ?></p>
                    <a href="<?= SITE_URL ?>/pages/client/chat.php?lawyer_id=<?= $l['id'] ?>" class="btn-secondary w-full flex items-center justify-center gap-2">
                        <span class="iconify" data-icon="lucide:message-circle"></span>
                        تواصل الآن
                    </a>
                </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-12">
                    <p class="text-noble-600">لا يوجد محامون مسجلون حالياً</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab 2: My Requests -->
    <div id="content-my-requests" class="tab-content hidden">
        <div class="card p-6">
            <h2 class="text-lg font-bold text-dark-700 mb-6">سجل طلبات الخدمات</h2>
            
            <?php if ($myRequests->num_rows > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-noble-200">
                                <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">الخدمة</th>
                                <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">المحامي</th>
                                <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">الحالة</th>
                                <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">التاريخ</th>
                                <th class="text-right py-3 px-2 text-sm font-semibold text-noble-600">التحكم</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($r = $myRequests->fetch_assoc()): ?>
                            <tr class="border-b border-noble-100 hover:bg-noble-50 transition-colors">
                                <td class="py-4 px-2 font-medium text-dark-700"><?= htmlspecialchars($r["service_name"]) ?></td>
                                <td class="py-4 px-2 text-noble-600"><?= htmlspecialchars($r["lawyer_name"] ?? "-") ?></td>
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
                                <td class="py-4 px-2 text-noble-600 text-sm"><?= $r["created_at"] ?></td>
                                <td class="py-4 px-2">
                                    <?php if ($r['status'] === 'pending'): ?>
                                        <div class="flex items-center gap-2">
                                            <button 
                                                data-id="<?= $r['id'] ?>" 
                                                data-desc="<?= htmlspecialchars($r['description'] ?? '') ?>"
                                                onclick="openEditModal(this)"
                                                class="text-blue-500 hover:text-blue-700" 
                                                title="تعديل">
                                                <span class="iconify" data-icon="lucide:edit"></span>
                                            </button>
                                            <form method="POST" action="<?= SITE_URL ?>/actions/cancel_request.php" class="inline" onsubmit="return confirm('هل أنت متأكد من إلغاء هذا الطلب؟')">
                                                <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                                <button type="submit" class="text-red-500 hover:text-red-700" title="إلغاء">
                                                    <span class="iconify" data-icon="lucide:trash-2"></span>
                                                </button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-noble-400 text-xs">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <div class="w-16 h-16 rounded-full bg-noble-200 flex items-center justify-center mx-auto mb-4">
                        <span class="iconify text-2xl text-noble-400" data-icon="lucide:file-text"></span>
                    </div>
                    <p class="text-noble-600">لا توجد طلبات سابقة</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modals and Scripts -->
    <style>
        .active-tab {
            background: linear-gradient(135deg, #C5A55A 0%, #B8944D 100%);
            color: #1F1B18;
            box-shadow: 0 4px 12px rgba(197, 165, 90, 0.2);
        }
    </style>

    <!-- Request Modal -->
    <div id="requestModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-dark-900/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden animate-fade-in-up">
            <div class="p-6 border-b border-noble-200 flex items-center justify-between bg-noble-50">
                <h3 class="text-xl font-bold text-dark-700">طلب خدمة: <span id="modalServiceName" class="text-gold-600"></span></h3>
                <button onclick="closeRequestModal()" class="text-noble-500 hover:text-dark-700">
                    <span class="iconify text-2xl" data-icon="lucide:x"></span>
                </button>
            </div>
            <form method="POST" action="<?= SITE_URL ?>/actions/request_service.php" class="p-6 space-y-4">
                <input type="hidden" name="service_id" id="modalServiceId">
                <input type="hidden" name="lawyer_id" id="modalLawyerId">
                
                <div>
                    <label class="block text-sm font-semibold text-dark-700 mb-2">تفاصيل الطلب (اختياري)</label>
                    <textarea name="description" placeholder="اشرح لنا باختصار ما تحتاجه في هذه الخدمة..." class="input-field min-h-[150px]"></textarea>
                </div>
                
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary flex-1">إرسال الطلب</button>
                    <button type="button" onclick="closeRequestModal()" class="btn-secondary">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Request Modal -->
    <div id="editRequestModal" class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-dark-900/50 backdrop-blur-sm p-4">
        <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl overflow-hidden animate-fade-in-up">
            <div class="p-6 border-b border-noble-200 flex items-center justify-between bg-noble-50">
                <h3 class="text-xl font-bold text-dark-700">تعديل طلب الخدمة</h3>
                <button onclick="closeEditModal()" class="text-noble-500 hover:text-dark-700">
                    <span class="iconify text-2xl" data-icon="lucide:x"></span>
                </button>
            </div>
            <form method="POST" action="<?= SITE_URL ?>/actions/update_request.php" class="p-6 space-y-4">
                <input type="hidden" name="request_id" id="editRequestId">
                
                <div>
                    <label class="block text-sm font-semibold text-dark-700 mb-2">تفاصيل الطلب</label>
                    <textarea name="description" id="editRequestDesc" placeholder="عدل تفاصيل طلبك هنا..." class="input-field min-h-[150px]"></textarea>
                </div>
                
                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary flex-1">حفظ التغييرات</button>
                    <button type="button" onclick="closeEditModal()" class="btn-secondary">إلغاء</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function switchTab(tabId) {
            // Hide all contents
            document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
            // Show target content
            document.getElementById('content-' + tabId).classList.remove('hidden');
            
            // Update buttons
            ['available', 'lawyers', 'my-requests'].forEach(id => {
                const btn = document.getElementById('tab-' + id);
                if (btn) {
                    btn.classList.remove('active-tab');
                    btn.classList.add('text-noble-600', 'hover:bg-noble-50');
                }
            });
            
            const activeBtn = document.getElementById('tab-' + tabId);
            if (activeBtn) {
                activeBtn.classList.add('active-tab');
                activeBtn.classList.remove('text-noble-600', 'hover:bg-noble-50');
            }
        }

        function openRequestModal(btn) {
            const serviceId = btn.getAttribute('data-id');
            const lawyerId = btn.getAttribute('data-lawyer');
            const serviceName = btn.getAttribute('data-name');
            
            document.getElementById('modalServiceId').value = serviceId;
            document.getElementById('modalLawyerId').value = lawyerId;
            document.getElementById('modalServiceName').innerText = serviceName;
            document.getElementById('requestModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeRequestModal() {
            document.getElementById('requestModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openEditModal(btn) {
            const requestId = btn.getAttribute('data-id');
            const desc = btn.getAttribute('data-desc');
            
            document.getElementById('editRequestId').value = requestId;
            document.getElementById('editRequestDesc').value = desc;
            document.getElementById('editRequestModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editRequestModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        window.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeRequestModal();
                closeEditModal();
            }
        });
    </script>
</div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>