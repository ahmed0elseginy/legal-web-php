<?php
require_once __DIR__ . "/../includes/auth.php";
redirectIfLoggedIn();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب — مكتب العدالة</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        justice: { 500: '#5c8A4d', 600: '#4a7040' },
                        noble: { 200: '#F5F0E8', 300: '#EDE5D8', 400: '#E0D4C3', 500: '#D1C1AA', 600: '#8A7860' },
                        gold: { 400: '#C5A55A', 500: '#B8944D', 600: '#A07E3C' },
                        dark: { 700: '#2A2520', 800: '#1F1B18', 900: '#151310' }
                    }
                }
            }
        }
    </script>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'IBM Plex Sans Arabic', sans-serif; background-color: #F5F0E8; }
    </style>
</head>
<body class="min-h-screen flex">

    <!-- Left Side - Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8">
        <div class="w-full max-w-md">
            <!-- Logo -->
            <a href="<?= SITE_URL ?>" class="flex items-center gap-3 mb-12">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-justice-500 to-justice-600 flex items-center justify-center shadow-lg">
                    <span class="iconify text-white text-xl" data-icon="lucide:scale"></span>
                </div>
                <div>
                    <span class="font-bold text-lg text-dark-700 block">مكتب العدالة</span>
                    <span class="text-xs text-gold-500 tracking-wider">JUSTICE LAW</span>
                </div>
            </a>

            <h1 class="text-2xl font-bold text-dark-700 mb-2">إنشاء حساب جديد</h1>
            <p class="text-noble-600 mb-8">انضم إلى مجتمع العدالة القانوني</p>

            <?php if ($flash): ?>
                <div class="bg-red-50 text-red-700 px-4 py-3 rounded-xl mb-6 flex items-center gap-2">
                    <span class="iconify" data-icon="lucide:alert-circle"></span>
                    <?= htmlspecialchars($flash['message']) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= SITE_URL ?>/actions/register.php" class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-dark-700 mb-2">الاسم الكامل</label>
                    <input type="text" name="name" 
                           class="w-full px-4 py-3 rounded-xl border border-noble-300 bg-white focus:outline-none focus:border-gold-500 focus:ring-2 focus:ring-gold-400/20" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-700 mb-2">البريد الإلكتروني</label>
                    <input type="email" name="email" id="register-email" 
                           class="w-full px-4 py-3 rounded-xl border border-noble-300 bg-white focus:outline-none focus:border-gold-500 focus:ring-2 focus:ring-gold-400/20" required>
                    <div id="email-feedback" class="text-sm mt-1"></div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-700 mb-2">كلمة المرور</label>
                    <input type="password" name="password" 
                           class="w-full px-4 py-3 rounded-xl border border-noble-300 bg-white focus:outline-none focus:border-gold-500 focus:ring-2 focus:ring-gold-400/20" required>
                </div>
                <div>
                    <label class="block text-sm font-medium text-dark-700 mb-2">نوع الحساب</label>
                    <select name="role" class="w-full px-4 py-3 rounded-xl border border-noble-300 bg-white focus:outline-none focus:border-gold-500 focus:ring-2 focus:ring-gold-400/20" required>
                        <option value="">اختر نوع الحساب</option>
                        <option value="client">موكل</option>
                        <option value="lawyer">محامي</option>
                    </select>
                </div>
                <button type="submit" class="w-full py-3.5 rounded-xl bg-gradient-to-r from-gold-400 to-gold-500 text-dark-800 font-semibold hover:from-gold-500 hover:to-gold-600 transition-all">
                    إنشاء حساب
                </button>
            </form>

            <p class="text-center text-noble-600 mt-8">
                لديك حساب بالفعل؟
                <a href="<?= SITE_URL ?>/pages/login.php" class="text-gold-600 font-semibold hover:underline">تسجيل الدخول</a>
            </p>

            <a href="<?= SITE_URL ?>/index.php" class="block text-center text-noble-500 mt-4 hover:text-gold-600">
                <span class="iconify inline" data-icon="lucide:arrow-right"></span>
                العودة للرئيسية
            </a>
        </div>
    </div>

    <!-- Right Side - Image -->
    <div class="hidden lg:block w-1/2 relative">
        <div class="absolute inset-0 bg-gradient-to-br from-justice-600 to-dark-700"></div>
        <div class="relative z-10 flex flex-col items-center justify-center h-full text-white p-12">
            <img src="<?= SITE_URL ?>/assets/images/security.png" alt="Security" class="w-48 mb-8 opacity-20 filter invert brightness-0">
            <h2 class="text-4xl font-bold text-center">انضم إلينا</h2>
            <p class="text-white/70 mt-4 text-xl text-center">خصوصيتك وأمانك أولويتنا</p>
        </div>
    </div>

</body>
</html>

<script>const SITE_URL = '<?= SITE_URL ?>';</script>
<script src="<?= SITE_URL ?>/assets/js/main.js"></script>