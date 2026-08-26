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
    
    <!-- Phosphor Icons (أيقونات هندسية حديثة) -->
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- ملف التنسيقات الرئيسي -->
    <link rel="stylesheet" href="assets/css/netpulse.css">
</head>
<body>

<div class="container">
   <header class="netpulse-navbar modern-navbar">
    <div class="brand-wrapper">
        <!-- الشعار البصري -->
        <div class="brand-logo-mark">
            <i class="ph-bold ph-activity"></i>
        </div>
        <!-- النص التعريفي -->
        <div class="brand">
            <h1>NetPulse <span>Portal</span></h1>
            <p>إدارة العمليات والدعم</p>
        </div>
    </div>

    <nav class="nav-links">
        <?php 
            $currentPage = $_GET['page'] ?? 'dashboard'; 
            // التأكد من بدء الجلسة للتحقق من الصلاحيات
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'ADMIN';
        ?>
        
        <a href="index.php?page=dashboard" class="nav-item <?php echo ($currentPage === 'dashboard') ? 'active' : ''; ?>">
            <i class="ph ph-squares-four"></i>
            لوحة التحكم
        </a>
        
        <a href="index.php?page=tickets" class="nav-item <?php echo ($currentPage === 'tickets') ? 'active' : ''; ?>">
            <i class="ph ph-ticket"></i>
            سجل التذاكر
        </a>

        <a href="index.php?page=tickets-create" class="nav-item nav-btn-action <?php echo ($currentPage === 'tickets-create') ? 'active' : ''; ?>">
            <i class="ph-bold ph-plus"></i>
            تذكرة جديدة
        </a>

        <!-- روابط الإدارة (تظهر فقط للمدير Admin) -->
        <?php if ($isAdmin): ?>
            <!-- تبويب إدارة المستخدمين -->
            <a href="index.php?page=users-list" class="nav-item <?php echo ($currentPage === 'users-list') ? 'active' : ''; ?>">
                <i class="ph ph-users"></i>
                إدارة المستخدمين
            </a>

            <!-- رابط إضافة مستخدم جديد -->
            <a href="index.php?page=users-create" class="nav-item <?php echo ($currentPage === 'users-create') ? 'active' : ''; ?>" style="border: 1px dashed var(--noc-primary, #3b82f6);">
                <i class="ph ph-user-plus"></i>
                إضافة مستخدم
            </a>
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