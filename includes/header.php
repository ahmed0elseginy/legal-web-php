<?php
require_once __DIR__ . "/auth.php";
$user = getUser();
$currentPage = $currentPage ?? "dashboard";
$pageTitle = $pageTitle ?? "لوحة التحكم";
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? "مكتب العدالة" ?> — مكتب العدالة</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:arabic:wght@300;400;500;600;700&family=Noto+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        justice: {
                            50: '#f4f7f1',
                            100: '#e6ede0',
                            200: '#cddbc2',
                            300: '#a8c290',
                            400: '#7da35e',
                            500: '#5c8A4d',
                            600: '#4a7040',
                            700: '#3b5a34',
                            800: '#2a452a',
                            900: '#2a3d26',
                            950: '#141f12',
                        },
                        noble: {
                            50: '#FDFBF7',
                            100: '#FAF6EE',
                            200: '#F5F0E8',
                            300: '#EDE5D8',
                            400: '#E0D4C3',
                            500: '#D1C1AA',
                            600: '#B8A888',
                            700: '#8A7860',
                            800: '#5C5040',
                            900: '#3D3528',
                        },
                        gold: {
                            300: '#D4B96A',
                            400: '#C5A55A',
                            500: '#B8944D',
                            600: '#A07E3C',
                        },
                        dark: {
                            700: '#2A2520',
                            800: '#1F1B18',
                            900: '#151310',
                        }
                    },
                    fontFamily: {
                        'sans': ['"IBM Plex Sans Arabic"', 'sans-serif'],
                        'arabic': ['"Noto Sans Arabic"', 'sans-serif'],
                    },
                    animation: {
                        'fade-in-up': 'fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards',
                        'fade-in': 'fadeIn 0.8s ease forwards',
                        'slide-in-right': 'slideInRight 0.5s ease forwards',
                    },
                    keyframes: {
                        fadeInUp: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                        fadeIn: {
                            '0%': { opacity: '0' },
                            '100%': { opacity: '1' },
                        },
                        slideInRight: {
                            '0%': { opacity: '0', transform: 'translateX(-20px)' },
                            '100%': { opacity: '1', transform: 'translateX(0)' },
                        }
                    }
                }
            }
        }
    </script>
    <style>
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { 
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            background-color: #F5F0E8;
        }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #F5F0E8; }
        ::-webkit-scrollbar-thumb { background: #C5A55A; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #B8944D; }
        .glass {
            background: rgba(253, 251, 247, 0.85);
            backdrop-filter: blur(20px);
        }
        .card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            transition: all 0.3s ease;
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background: linear-gradient(135deg, #C5A55A 0%, #B8944D 100%);
            color: #1F1B18;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(197, 165, 90, 0.3);
        }
        .btn-secondary {
            background: white;
            color: #3D3528;
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 500;
            border: 1px solid #E0D4C3;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background: #F5F0E8;
            border-color: #C5A55A;
        }
        .input-field {
            background: white;
            border: 1px solid #E0D4C3;
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 15px;
            transition: all 0.3s ease;
            width: 100%;
        }
        .input-field:focus {
            outline: none;
            border-color: #C5A55A;
            box-shadow: 0 0 0 4px rgba(197, 165, 90, 0.1);
        }
        .badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-pending { background: #FEF3C7; color: #92400E; }
        .badge-accepted { background: #D1FAE5; color: #065F46; }
        .badge-rejected { background: #FEE2E2; color: #991B1B; }
        .badge-active { background: #DBEAFE; color: #1E40AF; }
        .badge-completed { background: #E0E7FF; color: #3730A3; }
        .nav-link {
            padding: 12px 20px;
            border-radius: 12px;
            color: #5C5040;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .nav-link:hover {
            background: rgba(197, 165, 90, 0.1);
            color: #B8944D;
        }
        .nav-link.active {
            background: linear-gradient(135deg, #C5A55A 0%, #B8944D 100%);
            color: #1F1B18;
        }
        .hamburger {
            width: 24px;
            height: 18px;
            position: relative;
            cursor: pointer;
        }
        .hamburger span {
            display: block;
            position: absolute;
            height: 2px;
            width: 100%;
            background: #3D3528;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        .hamburger span:nth-child(1) { top: 0; }
        .hamburger span:nth-child(2) { top: 8px; }
        .hamburger span:nth-child(3) { top: 16px; }
        .fade-in { animation: fadeIn 0.5s ease forwards; }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body class="min-h-screen bg-noble-200">

    <!-- Mobile Header -->
    <header class="lg:hidden fixed top-0 left-0 right-0 z-50 glass border-b border-noble-300">
        <div class="flex items-center justify-between px-4 py-3">
            <a href="<?= SITE_URL ?>" class="flex items-center gap-2">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-justice-500 to-justice-600 flex items-center justify-center">
                    <span class="iconify text-white text-lg" data-icon="lucide:scale"></span>
                </div>
                <span class="font-bold text-dark-700">مكتب العدالة</span>
            </a>
            <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-noble-200">
                <span class="hamburger"><span></span><span></span><span></span></span>
            </button>
        </div>
        <div id="mobile-menu" class="hidden px-4 py-4 border-t border-noble-300 bg-noble-50/95">
            <?php include __DIR__ . "/sidebar-menu.php"; ?>
        </div>
    </header>

    <!-- Desktop Sidebar -->
    <aside class="hidden lg:flex flex-col fixed right-0 top-0 bottom-0 w-72 bg-white border-r border-noble-300 z-40">
        <!-- Logo -->
        <div class="p-6 border-b border-noble-200">
            <a href="<?= SITE_URL ?>" class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-justice-500 to-justice-600 flex items-center justify-center shadow-lg">
                    <span class="iconify text-white text-xl" data-icon="lucide:scale"></span>
                </div>
                <div>
                    <span class="font-bold text-lg text-dark-700 block">مكتب العدالة</span>
                    <span class="text-xs text-gold-500 tracking-wider">JUSTICE LAW</span>
                </div>
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
            <?php include __DIR__ . "/sidebar-menu.php"; ?>
        </nav>

        <!-- User Section -->
        <div class="p-4 border-t border-noble-200">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center bg-noble-100 border border-noble-200">
                    <img src="<?= SITE_URL ?>/assets/images/user.png" alt="Avatar" class="w-full h-full object-cover">
                </div>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-dark-700 truncate"><?= $user['name'] ?? 'مستخدم' ?></p>
                    <p class="text-xs text-noble-600 truncate"><?= $user['email'] ?? '' ?></p>
                </div>
            </div>
            <a href="<?= SITE_URL ?>/actions/logout.php" class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl border border-noble-300 text-noble-700 hover:bg-noble-100 transition-colors">
                <span class="iconify" data-icon="lucide:log-out"></span>
                تسجيل الخروج
            </a>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="lg:mr-72 pt-16 lg:pt-0 min-h-screen">