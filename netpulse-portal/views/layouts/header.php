<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NetPulse Portal | نظام تذاكر العمليات</title>
    
    <!-- Google Fonts: Inter & IBM Plex Sans Arabic -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Phosphor Icons -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- ملف التنسيقات الرئيسي -->
    <link rel="stylesheet" href="assets/css/netpulse.css">

    <!-- تنسيقات القوائم المنسدلة المُصلحة -->
    <style>
        .nav-dropdown {
            position: relative;
            display: inline-flex;
            align-items: center;
        }
        
        .nav-dropdown .dropdown-menu {
            display: flex; /* إبقاء القائمة Flex دائماً والتحكم في الظهور عبر Opacity */
            flex-direction: column;
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--card-bg, #ffffff);
            border: 1px solid var(--border-color, #e5e7eb);
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            min-width: 180px;
            z-index: 1000;
            padding: 8px;
            margin-top: 6px;
            
            /* حركات السلاسة وتأخير الاختفاء */
            opacity: 0;
            visibility: hidden;
            transform: translateY(6px);
            transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s ease;
            pointer-events: none; /* منع النقر عليها وهي مخفية */
        }

        /* جسر وهمي شفاف يمنع اختفاء القائمة عند تحريك الماوس بين الزر والقائمة */
        .nav-dropdown .dropdown-menu::before {
            content: '';
            position: absolute;
            top: -12px;
            left: 0;
            right: 0;
            height: 12px;
            background: transparent;
        }

        /* إظهار القائمة عند المرور */
        .nav-dropdown:hover .dropdown-menu {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
            pointer-events: auto; /* تفعيل النقر عند الظهور */
        }

        /* تنسيق الأزرار داخل القائمة */
        .dropdown-menu .nav-item {
            display: flex !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 12px !important;
            width: 100% !important;
            box-sizing: border-box;
            padding: 10px 14px !important;
            margin-bottom: 4px;
            white-space: nowrap;
            text-decoration: none;
            border-radius: 6px;
        }

        .dropdown-menu .nav-item:last-child {
            margin-bottom: 0;
        }

        .dropdown-menu .nav-item i {
            margin: 0 !important;
            font-size: 18px !important;
        }

        .nav-dropdown > .nav-item {
            display: flex;
            align-items: center;
            gap: 6px;
        }
    </style>
</head>
<body>

<div class="container">
   <header class="netpulse-navbar modern-navbar">
    <div class="brand-wrapper">
        <div class="brand-logo-mark">
            <i class="ph-bold ph-activity"></i>
        </div>
        <div class="brand">
            <h1>NetPulse <span>Portal</span></h1>
            <p>إدارة العمليات والدعم</p>
        </div>
    </div>

    <nav class="nav-links">
        <?php 
            $currentPage = $_GET['page'] ?? 'dashboard'; 
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'ADMIN';
        ?>
        
        <a href="index.php?page=dashboard" class="nav-item <?php echo ($currentPage === 'dashboard') ? 'active' : ''; ?>">
            <i class="ph ph-squares-four"></i>
            لوحة التحكم
        </a>
        
        <!-- قائمة منسدلة للتذاكر -->
        <div class="nav-dropdown">
            <a href="#" class="nav-item <?php echo ($currentPage === 'tickets' || $currentPage === 'tickets-create') ? 'active' : ''; ?>" onclick="return false;" style="cursor: pointer;">
                <i class="ph ph-ticket"></i>
                <span>التذاكر</span>
                <i class="ph ph-caret-down" style="font-size: 12px;"></i>
            </a>
            <div class="dropdown-menu">
                <a href="index.php?page=tickets" class="nav-item <?php echo ($currentPage === 'tickets') ? 'active' : ''; ?>">
                    <i class="ph ph-ticket"></i>
                    <span>سجل التذاكر</span>
                </a>
                <a href="index.php?page=tickets-create" class="nav-item <?php echo ($currentPage === 'tickets-create') ? 'active' : ''; ?>">
                    <i class="ph-bold ph-plus"></i>
                    <span>تذكرة جديدة</span>
                </a>
            </div>
        </div>

        <!-- روابط الإدارة (تظهر فقط للمدير Admin) -->
        <?php if ($isAdmin): ?>
            <!-- قائمة منسدلة لإدارة المستخدمين -->
            <div class="nav-dropdown">
                <a href="#" class="nav-item <?php echo ($currentPage === 'users-list' || $currentPage === 'users-create') ? 'active' : ''; ?>" onclick="return false;" style="cursor: pointer;">
                    <i class="ph ph-users"></i>
                    <span>المستخدمين</span>
                    <i class="ph ph-caret-down" style="font-size: 12px;"></i>
                </a>
                <div class="dropdown-menu">
                    <a href="index.php?page=users-list" class="nav-item <?php echo ($currentPage === 'users-list') ? 'active' : ''; ?>">
                        <i class="ph ph-users"></i>
                        <span>إدارة المستخدمين</span>
                    </a>
                    <a href="index.php?page=users-create" class="nav-item <?php echo ($currentPage === 'users-create') ? 'active' : ''; ?>" style="border: 1px dashed var(--noc-primary, #3b82f6);">
                        <i class="ph ph-user-plus"></i>
                        <span>إضافة مستخدم</span>
                    </a>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- زر تسجيل الخروج -->
        <a href="index.php?page=logout" class="nav-item" style="color: #fca5a5;" title="تسجيل الخروج">
            <i class="ph ph-sign-out"></i>
        </a>

        <!-- زر الإشعارات -->
        <button class="icon-btn" aria-label="الإشعارات" style="background: none; border: 1px solid var(--border-color); padding: 8px 10px; border-radius: 6px; cursor: pointer; color: var(--text-secondary); display: flex; align-items: center; justify-content: center;">
            <i class="ph ph-bell" style="font-size: 16px;"></i>
        </button>
    </nav>
</header>
