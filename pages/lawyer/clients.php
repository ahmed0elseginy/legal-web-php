<?php
require_once __DIR__ . "/../../includes/auth.php";
requireRole("lawyer");

$conn = getDb();
$user = getUser();
$pageTitle = "الموكلين";
$currentPage = "clients";

$stmt = $conn->prepare("SELECT u.id, u.name, u.email, COUNT(c.id) as cases_count FROM users u JOIN cases c ON u.id = c.client_id WHERE c.lawyer_id = ? GROUP BY u.id, u.name, u.email");
$stmt->bind_param("i", $user["id"]);
$stmt->execute();
$clients = $stmt->get_result();

include __DIR__ . "/../../includes/header.php";
?>

<!-- Page Header -->
<div class="p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl lg:text-3xl font-bold text-dark-700">الموكلين</h1>
        <p class="text-noble-600 mt-1">إدارة ومتابعة بيانات الموكلين</p>
    </div>

    <!-- Clients Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if ($clients->num_rows > 0): ?>
            <?php while ($cl = $clients->fetch_assoc()): ?>
            <div class="card p-6 flex items-center gap-4">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-justice-500 to-justice-600 flex items-center justify-center text-white font-bold text-xl">
                    <?= mb_substr($cl["name"], 0, 1) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="font-semibold text-dark-700 truncate"><?= htmlspecialchars($cl["name"]) ?></h3>
                    <p class="text-sm text-noble-500 truncate"><?= htmlspecialchars($cl["email"]) ?></p>
                    <div class="flex items-center justify-between mt-2">
                        <span class="text-sm text-gold-600 font-medium"><?= $cl["cases_count"] ?> قضايا</span>
                        <a href="<?= SITE_URL ?>/pages/lawyer/chat.php?client_id=<?= $cl['id'] ?>" class="p-2 rounded-lg bg-noble-100 text-gold-600 hover:bg-gold-500 hover:text-white transition-all shadow-sm" title="محادثة">
                            <span class="iconify text-xl" data-icon="lucide:message-circle"></span>
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
        <div class="col-span-full text-center py-16">
            <div class="w-20 h-20 rounded-full bg-noble-200 flex items-center justify-center mx-auto mb-4">
                <span class="iconify text-3xl text-noble-400" data-icon="lucide:users"></span>
            </div>
            <p class="text-noble-600 text-lg">لا يوجد موكلين بعد</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>