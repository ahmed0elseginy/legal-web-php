<?php
session_start();

require_once __DIR__ . "/includes/config.php";

if (isset($_SESSION["user_role"])) {
    if ($_SESSION["user_role"] === "lawyer") {
        header("Location: " . SITE_URL . "/pages/lawyer/dashboard.php");
    } else {
        header("Location: " . SITE_URL . "/pages/client/dashboard.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مكتب العدالة — Justice Law</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        justice: { 500: '#5c8A4d', 600: '#4a7040' },
                        noble: { 100: '#FAF6EE', 200: '#F5F0E8', 300: '#EDE5D8', 400: '#E0D4C3', 500: '#D1C1AA', 600: '#8A7860' },
                        gold: { 300: '#D4B96A', 400: '#C5A55A', 500: '#B8944D', 600: '#A07E3C' },
                        dark: { 700: '#2A2520', 800: '#1F1B18', 900: '#151310' }
                    }
                }
            }
        }
    </script>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'IBM Plex Sans Arabic', sans-serif; }
        .hero-gradient {
            background: linear-gradient(135deg, rgba(31, 27, 24, 0.85) 0%, rgba(42, 37, 32, 0.85) 100%), url('<?= SITE_URL ?>/assets/images/home.png');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="min-h-screen bg-noble-200">

    <nav class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-xl border-b border-noble-300">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <a href="<?= SITE_URL ?>" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-justice-500 to-justice-600 flex items-center justify-center shadow-lg">
                    <span class="iconify text-white text-lg" data-icon="lucide:scale"></span>
                </div>
                <span class="font-bold text-lg text-dark-700">مكتب العدالة</span>
            </a>
            <div class="hidden md:flex items-center gap-8">
                <a href="#features" class="text-dark-700 hover:text-gold-600 transition-colors">المميزات</a>
                <a href="#about" class="text-dark-700 hover:text-gold-600 transition-colors">عن المنصة</a>
                <a href="<?= SITE_URL ?>/pages/login.php" class="px-5 py-2.5 rounded-xl border border-dark-700 text-dark-700 hover:bg-dark-700 hover:text-white transition-all">دخول</a>
                <a href="<?= SITE_URL ?>/pages/register.php" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-gold-400 to-gold-500 text-dark-800 font-semibold hover:from-gold-500 hover:to-gold-600 transition-all">إنشاء حساب</a>
            </div>
        </div>
    </nav>

    <section class="hero-gradient min-h-screen flex items-center pt-20">
        <div class="max-w-7xl mx-auto px-6 py-20 grid lg:grid-cols-2 gap-16 items-center">
            <div class="text-white">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white/10 text-gold-400 text-sm mb-6">
                    <span class="w-2 h-2 rounded-full bg-gold-400"></span>
                    منذ 2026
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
                    مستقبل <span class="text-gold-400">الخدمات القانونية</span><br>بين يديك
                </h1>
                <p class="text-noble-300 text-lg mb-8 max-w-lg">
                    نظام شامل لإدارة القضايا والموكلين مدعوم بأحدث التقنيات. تجربة استثنائية للمحامين والمستشارين القانونيين
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="<?= SITE_URL ?>/pages/register.php" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl bg-gold-500 text-dark-800 font-semibold hover:bg-gold-400 transition-all">
                        ابدأ الآن
                        <span class="iconify" data-icon="lucide:arrow-left"></span>
                    </a>
                    <a href="#features" class="inline-flex items-center gap-2 px-8 py-4 rounded-xl border border-white/30 text-white hover:bg-white/10 transition-all">
                        اعرف المزيد
                    </a>
                </div>
                
                <div class="flex flex-wrap gap-8 mt-12">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-gold-400">24/7</p>
                        <p class="text-noble-400 text-sm">دعم فني</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-gold-400">98%+</p>
                        <p class="text-noble-400 text-sm">موكل سعيد</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-gold-400">500+</p>
                        <p class="text-noble-400 text-sm">محامي نشط</p>
                    </div>
                </div>
            </div>
            <div class="hidden lg:flex items-center justify-center">
                <span class="iconify text-[200px] text-white/5" data-icon="lucide:scale"></span>
            </div>
        </div>
    </section>

    <section id="features" class="py-24 bg-noble-200">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <span class="inline-flex items-center gap-2 text-gold-500 text-sm tracking-wider mb-4">
                    <span class="w-6 h-px bg-gold-500"></span>
                    المميزات
                    <span class="w-6 h-px bg-gold-500"></span>
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-dark-700">لماذا تختار مكتب العدالة؟</h2>
            </div>
            
            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-white rounded-2xl p-8 text-center shadow-lg">
                    <div class="w-16 h-16 rounded-2xl bg-justice-100 flex items-center justify-center mx-auto mb-4">
                        <span class="iconify text-3xl text-justice-600" data-icon="lucide:briefcase"></span>
                    </div>
                    <h3 class="text-xl font-bold text-dark-700 mb-2">إدارة القضايا</h3>
                    <p class="text-noble-600">نظام متكامل لإدارة القضايا والمتابعة اليومية مع العملاء</p>
                </div>
                <div class="bg-white rounded-2xl p-8 text-center shadow-lg">
                    <div class="w-16 h-16 rounded-2xl bg-gold-100 flex items-center justify-center mx-auto mb-4">
                        <span class="iconify text-3xl text-gold-600" data-icon="lucide:calendar"></span>
                    </div>
                    <h3 class="text-xl font-bold text-dark-700 mb-2">المواعيد</h3>
                    <p class="text-noble-600">جدولة دقيقة للمواعيد ومتابعة الحضور بسهولة</p>
                </div>
                <div class="bg-white rounded-2xl p-8 text-center shadow-lg">
                    <div class="w-16 h-16 rounded-2xl bg-justice-100 flex items-center justify-center mx-auto mb-4">
                        <span class="iconify text-3xl text-justice-600" data-icon="lucide:message-circle"></span>
                    </div>
                    <h3 class="text-xl font-bold text-dark-700 mb-2">التواصل</h3>
                    <p class="text-noble-600">محادثة مباشرة مع العملاء والمحامين في مكان واحد</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 hero-gradient">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">جاهز لنقل مكتبك للمستقبل؟</h2>
            <p class="text-noble-300 text-lg mb-8">انضم إلى مئات المحامين واستفد من خدماتنا المتكاملة</p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="<?= SITE_URL ?>/pages/register.php" class="px-8 py-4 rounded-xl bg-gold-500 text-dark-800 font-semibold hover:bg-gold-400 transition-all">ابدأ الآن</a>
                <a href="<?= SITE_URL ?>/pages/login.php" class="px-8 py-4 rounded-xl border border-white/30 text-white hover:bg-white/10 transition-all">دخول</a>
            </div>
        </div>
    </section>

    <footer class="bg-dark-900 py-12">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-justice-500 flex items-center justify-center">
                        <span class="iconify text-white text-lg" data-icon="lucide:scale"></span>
                    </div>
                    <span class="font-bold text-lg text-white">مكتب العدالة</span>
                </div>
                <div class="flex items-center gap-6 text-noble-400 text-sm">
                    <a href="#" class="hover:text-gold-400">الخصوصية</a>
                    <a href="#" class="hover:text-gold-400">الشروط</a>
                    <a href="#" class="hover:text-gold-400">تواصل معنا</a>
                </div>
                <p class="text-noble-500 text-sm">© 2026 مكتب العدالة</p>
            </div>
        </div>
    </footer>

</body>
</html>