<?php
require_once __DIR__ . "/../../includes/auth.php";
requireRole("lawyer");

$conn = getDb();
$user = getUser();
$pageTitle = "طلبات الخدمة - المحامي";
$currentPage = "requests";

$stmt = $conn->prepare("SELECT * FROM service_requests WHERE lawyer_id = ? OR lawyer_id IS NULL OR lawyer_id = 0 ORDER BY created_at DESC");
$stmt->bind_param("i", $user["id"]);
$stmt->execute();
$requests = $stmt->get_result();

include __DIR__ . "/../../includes/header.php";
include __DIR__ . "/../../includes/lawyer-sidebar.php";
?>

<main class="main-content">
    <div class="top-bar">
        <div class="search-box"><i class="fa-solid fa-magnifying-glass"></i><input type="text" placeholder="ابحث في الطلبات..."></div>
        <div class="top-actions">
            <div class="user-avatar"><?= strtoupper(substr($user["name"], 0, 1)) ?></div>
            <a href="<?= SITE_URL ?>/actions/logout.php" class="btn-logout"><i class="fa-solid fa-right-from-bracket"></i></a>
        </div>
    </div>

    <div class="page-header">
        <h1>طلبات الخدمة</h1>
        <p>الطلبات الجديدة المقدمة من الموكلين</p>
    </div>

    <section class="content-card">
        <table class="data-table">
            <thead>
                <tr>
                    <th>الموكل</th>
                    <th>الخدمة المطلوبة</th>
                    <th>الحالة</th>
                    <th>التاريخ</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($requests->num_rows > 0): ?>
                    <?php while ($r = $requests->fetch_assoc()): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($r["client_name"]) ?></strong></td>
                        <td><?= htmlspecialchars($r["service_name"]) ?></td>
                        <td><span class="badge badge-<?= $r['status'] ?>"><?= $r["status"] ?></span></td>
                        <td><?= $r["created_at"] ?></td>
                        <td>
                            <?php if ($r['status'] === 'pending'): ?>
                                <form method="POST" action="<?= SITE_URL ?>/actions/handle_request.php" style="display:inline;">
                                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                    <input type="hidden" name="action" value="accepted">
                                    <button type="submit" class="btn btn-sm btn-primary">قبول</button>
                                </form>
                                <form method="POST" action="<?= SITE_URL ?>/actions/handle_request.php" style="display:inline;">
                                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                    <input type="hidden" name="action" value="rejected">
                                    <button type="submit" class="btn btn-sm btn-secondary">رفض</button>
                                </form>
                            <?php else: ?>
                                <span class="badge badge-<?= $r['status'] ?>"><?= $r["status"] ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5" class="empty-state">لا توجد طلبات جديدة</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </section>
</main>

<?php include __DIR__ . "/../../includes/footer.php"; ?>
