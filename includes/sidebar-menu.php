<?php
$currentPage = $currentPage ?? "";

$clientLinks = [
    'dashboard' => ['icon' => 'lucide:layout-dashboard', 'label' => 'الرئيسية', 'url' => SITE_URL . '/pages/client/dashboard.php'],
    'marketplace' => ['icon' => 'lucide:store', 'label' => 'الخدمات', 'url' => SITE_URL . '/pages/client/marketplace.php'],
    'cases' => ['icon' => 'lucide:briefcase', 'label' => 'قضاياي', 'url' => SITE_URL . '/pages/client/cases.php'],
    'chat' => ['icon' => 'lucide:message-circle', 'label' => 'المحادثة', 'url' => SITE_URL . '/pages/client/chat.php'],
];

$lawyerLinks = [
    'dashboard' => ['icon' => 'lucide:layout-dashboard', 'label' => 'الرئيسية', 'url' => SITE_URL . '/pages/lawyer/dashboard.php'],
    'marketplace' => ['icon' => 'lucide:store', 'label' => 'الخدمات', 'url' => SITE_URL . '/pages/lawyer/marketplace.php'],
    'cases' => ['icon' => 'lucide:briefcase', 'label' => 'القضايا', 'url' => SITE_URL . '/pages/lawyer/cases.php'],
    'chat' => ['icon' => 'lucide:message-circle', 'label' => 'المحادثة', 'url' => SITE_URL . '/pages/lawyer/chat.php'],
    'clients' => ['icon' => 'lucide:users', 'label' => 'العملاء', 'url' => SITE_URL . '/pages/lawyer/clients.php'],
];

$role = $_SESSION['user_role'] ?? 'client';
$links = $role === 'lawyer' ? $lawyerLinks : $clientLinks;
?>

<div class="space-y-1">
    <?php foreach ($links as $key => $link): ?>
        <a href="<?= $link['url'] ?>" class="nav-link <?= $key === $currentPage ? 'active' : '' ?>">
            <span class="iconify text-lg" data-icon="<?= $link['icon'] ?>"></span>
            <?= $link['label'] ?>
        </a>
    <?php endforeach; ?>
</div>