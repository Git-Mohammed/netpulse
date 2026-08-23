<?php
/**
 * NetPulse Portal - Front Controller / Router
 */

// استدعاء وحدة التحكم الخاصة بالتذاكر
require_once __DIR__ . '/../src/Controllers/TicketController.php';

$controller = new TicketController();
$page = $_GET['page'] ?? 'dashboard';

// جلب البيانات (التذاكر والفلاتر) ليتم مشاركتها مع العروض
$data = $controller->index();
$tickets = $data['tickets'] ?? [];

// تضمين رأس الصفحة المشترك (Header)
include __DIR__ . '/../views/layouts/header.php';

// نظام التوجيه (Routing) بناءً على قيمة المتغير page
switch ($page) {
    case 'dashboard':
        include __DIR__ . '/../views/dashboard/index.php';
        break;

    case 'tickets':
        include __DIR__ . '/../views/tickets/index.php';
        break;

    case 'tickets-create':
        include __DIR__ . '/../views/tickets/create.php';
        break;

    case 'tickets-show':    
        $ticketId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $ticket = $controller->show($ticketId);
        include __DIR__ . '/../views/tickets/show.php';
        break;
case 'tickets-update-status':
        $controller->updateStatus();
        break;
    default:
        echo '<div class="table-section" style="padding: 40px; text-align: center; color: var(--critical-color);">'
           . '<h3>الصفحة المطلوبة غير موجودة (404)</h3>'
           . '<p style="color: var(--text-secondary); margin-top: 10px;">عذراً، المسار الذي تحاول الوصول إليه غير متوفر في النظام.</p>'
           . '</div>';
        break;
}

// تضمين تذييلة الصفحة المشتركة (Footer)
include __DIR__ . '/../views/layouts/footer.php';