<?php $current = $currentPage ?? ""; ?>
<aside class="sidebar">
    <div class="sidebar-logo">
        <img src="<?= SITE_URL ?>/assets/images/logoo.png" alt="MIZAN">
        <span>بوابة المحامي</span>
    </div>

    <nav class="sidebar-nav">
        <p class="nav-label">المحامي</p>
        <ul>
            <li class="<?= $current === 'dashboard' ? 'active' : '' ?>">
                <a href="<?= SITE_URL ?>/pages/lawyer/dashboard.php">
                    <i class="fa-solid fa-table-cells-large"></i>
                    <span>لوحة المعلومات</span>
                </a>
            </li>
            <li class="<?= $current === 'clients' ? 'active' : '' ?>">
                <a href="<?= SITE_URL ?>/pages/lawyer/clients.php">
                    <i class="fa-regular fa-user"></i>
                    <span>الموكلين</span>
                </a>
            </li>
            <li class="<?= $current === 'requests' ? 'active' : '' ?>">
                <a href="<?= SITE_URL ?>/pages/lawyer/requests.php">
                    <i class="fa-regular fa-clipboard"></i>
                    <span>طلبات الخدمة</span>
                </a>
            </li>
        </ul>

        <div class="nav-divider"></div>
        <p class="nav-label">القائمة الرئيسية</p>
        <ul>
            <li class="<?= $current === 'cases' ? 'active' : '' ?>">
                <a href="<?= SITE_URL ?>/pages/lawyer/cases.php">
                    <i class="fa-solid fa-briefcase"></i>
                    <span>القضايا</span>
                </a>
            </li>
            <li class="<?= $current === 'appointments' ? 'active' : '' ?>">
                <a href="<?= SITE_URL ?>/pages/lawyer/appointments.php">
                    <i class="fa-regular fa-calendar"></i>
                    <span>المواعيد</span>
                </a>
            </li>
            <li class="<?= $current === 'chat' ? 'active' : '' ?>">
                <a href="<?= SITE_URL ?>/pages/lawyer/chat.php">
                    <i class="fa-regular fa-message"></i>
                    <span>المحادثات</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>
