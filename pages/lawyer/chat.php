<?php
require_once __DIR__ . "/../../includes/auth.php";
requireRole("lawyer");

$conn = getDb();
$user = getUser();
$pageTitle = "المحادثات - المحامي";
$currentPage = "chat";

$stmt = $conn->prepare("SELECT DISTINCT u.id, u.name, u.email FROM users u JOIN messages m ON (m.sender_id = u.id OR m.receiver_id = u.id) WHERE u.role = 'client' AND (m.sender_id = ? OR m.receiver_id = ?) GROUP BY u.id ORDER BY MAX(m.created_at) DESC");
$stmt->bind_param("ii", $user["id"], $user["id"]);
$stmt->execute();
$conversations = $stmt->get_result();

$selectedId = $_GET["client_id"] ?? null;
$messages = [];

if ($selectedId) {
    $stmt = $conn->prepare("SELECT m.*, u.name as sender_name FROM messages m JOIN users u ON m.sender_id = u.id WHERE (m.sender_id = ? AND m.receiver_id = ?) OR (m.sender_id = ? AND m.receiver_id = ?) ORDER BY m.created_at ASC");
    $stmt->bind_param("iiii", $user["id"], $selectedId, $selectedId, $user["id"]);
    $stmt->execute();
    $messages = $stmt->get_result();
}

include __DIR__ . "/../../includes/header.php";
?>
<!-- Chat Interface -->
<div class="h-[calc(100vh-2rem)] p-4 lg:p-8 flex flex-col">
    <div class="mb-6">
        <h1 class="text-2xl lg:text-3xl font-bold text-dark-700">المحادثات</h1>
        <p class="text-noble-600 mt-1">تواصل مع موكليك لمناقشة تفاصيل قضاياهم</p>
    </div>

    <div class="flex-1 flex gap-6 overflow-hidden">
        <!-- Sidebar: Conversations List -->
        <div class="w-full lg:w-80 flex flex-col gap-4 <?= $selectedId ? 'hidden lg:flex' : 'flex' ?>">
            <div class="card flex-1 flex flex-col overflow-hidden">
                <div class="p-4 border-b border-noble-100 bg-noble-50/50">
                    <h2 class="font-bold text-dark-700 flex items-center gap-2">
                        <span class="iconify text-gold-600" data-icon="lucide:messages-square"></span>
                        المحادثات الأخيرة
                    </h2>
                </div>
                <div class="flex-1 overflow-y-auto p-2 space-y-1">
                    <?php if ($conversations->num_rows > 0): ?>
                        <?php while ($c = $conversations->fetch_assoc()): ?>
                        <a href="?client_id=<?= $c['id'] ?>" class="flex items-center gap-3 p-3 rounded-xl transition-all <?= $selectedId == $c['id'] ? 'bg-justice-100 text-justice-900 border border-justice-200' : 'hover:bg-noble-50 text-noble-700' ?>">
                            <div class="w-10 h-10 rounded-full bg-justice-600 flex items-center justify-center text-white font-bold shrink-0 shadow-sm">
                                <?= mb_substr($c["name"], 0, 1) ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-semibold truncate"><?= htmlspecialchars($c["name"]) ?></p>
                                <p class="text-xs opacity-70 truncate"><?= htmlspecialchars($c["email"]) ?></p>
                            </div>
                        </a>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-12">
                            <span class="iconify text-4xl text-noble-300 mx-auto mb-2" data-icon="lucide:message-square-dashed"></span>
                            <p class="text-noble-500 text-sm">لا توجد محادثات مع موكلين</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Chat Window -->
        <div class="flex-1 flex flex-col h-full <?= !$selectedId ? 'hidden lg:flex' : 'flex' ?>">
            <?php if ($selectedId): ?>
                <?php 
                $stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
                $stmt->bind_param("i", $selectedId);
                $stmt->execute();
                $otherUser = $stmt->get_result()->fetch_assoc();
                ?>
                <div class="card flex-1 flex flex-col overflow-hidden relative">
                    <!-- Chat Header -->
                    <div class="p-4 border-b border-noble-100 bg-white flex items-center justify-between sticky top-0 z-10">
                        <div class="flex items-center gap-3">
                            <a href="chat.php" class="lg:hidden text-noble-500">
                                <span class="iconify text-2xl" data-icon="lucide:arrow-right"></span>
                            </a>
                            <div class="w-10 h-10 rounded-full bg-justice-500 flex items-center justify-center text-white font-bold shadow-sm">
                                <?= mb_substr($otherUser['name'] ?? 'م', 0, 1) ?>
                            </div>
                            <div>
                                <h3 class="font-bold text-dark-700"><?= htmlspecialchars($otherUser['name'] ?? 'موكل') ?></h3>
                                <p class="text-xs text-green-600 flex items-center gap-1">
                                    <span class="w-2 h-2 rounded-full bg-green-500"></span> نشط الآن
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Messages List -->
                    <div id="chatMessages" class="flex-1 overflow-y-auto p-4 lg:p-6 space-y-4 bg-noble-50/30">
                        <?php while ($m = $messages->fetch_assoc()): ?>
                        <div class="flex <?= $m['sender_id'] == $user['id'] ? 'justify-start' : 'justify-end' ?>">
                            <div class="max-w-[80%] flex flex-col <?= $m['sender_id'] == $user['id'] ? 'items-start' : 'items-end' ?>">
                                <div class="p-3 lg:p-4 rounded-2xl shadow-sm <?= $m['sender_id'] == $user['id'] ? 'bg-white text-dark-700 rounded-tr-none border border-noble-200' : 'bg-justice-600 text-white rounded-tl-none' ?>">
                                    <p class="leading-relaxed text-[15px]"><?= htmlspecialchars($m["content"]) ?></p>
                                </div>
                                <span class="text-[10px] text-noble-500 mt-1 px-1"><?= date('H:i', strtotime($m["created_at"])) ?></span>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>

                    <!-- Chat Input -->
                    <div class="p-4 bg-white border-t border-noble-100">
                        <form method="POST" action="<?= SITE_URL ?>/actions/send_message.php" class="flex items-center gap-2">
                            <input type="hidden" name="receiver_id" value="<?= $selectedId ?>">
                            <input type="text" name="content" placeholder="اكتب رسالتك هنا..." required 
                                class="flex-1 bg-noble-50 border border-noble-200 rounded-xl px-4 py-3 focus:outline-none focus:border-justice-500 focus:ring-1 focus:ring-justice-500 transition-all">
                            <button type="submit" class="w-12 h-12 rounded-xl bg-justice-600 text-white flex items-center justify-center hover:bg-justice-700 transition-all shadow-md active:scale-95 shrink-0">
                                <span class="iconify text-2xl" data-icon="lucide:send-horizonal"></span>
                            </button>
                        </form>
                    </div>
                </div>
                <script>
                    const chatMessages = document.getElementById('chatMessages');
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                </script>
            <?php else: ?>
                <div class="card flex-1 flex flex-col items-center justify-center text-center p-8 bg-noble-50/20 border-dashed border-2 border-noble-300">
                    <div class="mb-4">
                        <img src="<?= SITE_URL ?>/assets/images/chat.png" alt="Chat" class="w-32 opacity-50 grayscale">
                    </div>
                    <h3 class="text-xl font-bold text-dark-700">ابدأ المحادثة</h3>
                    <p class="text-noble-600 mt-2 max-w-xs">اختر موكلاً من القائمة الجانبية للرد على استفساراته</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . "/../../includes/footer.php"; ?>
