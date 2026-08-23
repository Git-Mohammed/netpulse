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
    <!-- شريط التنقل العلوي الحديث (Modern Navbar) -->
    <header class="netpulse-navbar modern-navbar">
        <div class="brand-wrapper">
            <!-- الشعار البصري -->
            <div class="brand-logo-mark">
                <i class="ph-bold ph-activity"></i>
            </div>
            <!-- النص التعريفي -->
            <div class="brand-content">
                <h1>NetPulse <span class="font-light">Portal</span></h1>
                <span class="brand-badge">إدارة العمليات والدعم</span>
            </div>
        </div>

        <nav class="nav-links">
            <?php 
                $currentPage = $_GET['page'] ?? 'dashboard'; 
            ?>
            <a href="index.php?page=dashboard" class="nav-item <?php echo ($currentPage === 'dashboard') ? 'active' : ''; ?>">
                <i class="ph ph-squares-four"></i>
                لوحة التحكم
            </a>
            
            <a href="index.php?page=tickets" class="nav-item <?php echo ($currentPage === 'tickets') ? 'active' : ''; ?>">
                <i class="ph ph-ticket"></i>
                سجل التذاكر
            </a>
            
            <!-- فاصل بصري عمودي -->
            <div class="nav-divider"></div>

            <a href="index.php?page=tickets-create" class="btn-primary">
                <i class="ph-bold ph-plus"></i>
                تذكرة جديدة
            </a>
            
            <!-- زر الإشعارات / الملف الشخصي (لمسة حديثة للأنظمة) -->
            <button class="icon-btn" aria-label="الإشعارات">
                <i class="ph ph-bell"></i>
            </button>
        </nav>
    </header>