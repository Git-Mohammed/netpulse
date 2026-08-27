<?php
/**
 * NetPulse Portal - Front Controller / Central Router
 * 
 * @package NetPulse\Core
 * @version 1.1.2
 * @author   NetPulse Development Team
 */

declare(strict_types=1);

// تفعيل التخزين المؤقت لمنع أخطاء إرسال الترويسات المبكرة
ob_start();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// -------------------------------------------------------------------------
// 1. Dependency Imports
// -------------------------------------------------------------------------
require_once __DIR__ . '/../src/Controllers/TicketController.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Repositories/UserRepository.php';

// -------------------------------------------------------------------------
// 2. Controller Initialization
// -------------------------------------------------------------------------
$ticketController = new TicketController();
$authController   = new AuthController();
$userRepo         = new UserRepository();

$userRepo->ensureDefaultAdminExists(); // سيتم إنشاء المستخدم تلقائياً إذا كان الجدول فارغاً
$page = filter_input(INPUT_GET, 'page', FILTER_DEFAULT) ?? 'dashboard';

// -------------------------------------------------------------------------
// 3. Authentication Middleware & Security Guards
// -------------------------------------------------------------------------
$publicPages = ['login', 'login-submit'];
$isAuthenticated = isset($_SESSION['user_id']);

if (!$isAuthenticated && !in_array($page, $publicPages, true)) {
    header('Location: /index.php?page=login');
    exit;
}

if ($isAuthenticated && in_array($page, $publicPages, true)) {
    header('Location: /index.php?page=dashboard');
    exit;
}

// -------------------------------------------------------------------------
// 4. Standalone Authentication Routes
// -------------------------------------------------------------------------
switch ($page) {
    case 'login':
        $authController->showLoginForm();
        exit;

    case 'login-submit':
        $authController->login();
        exit;

    case 'logout':
        $authController->logout();
        exit;
}

// -------------------------------------------------------------------------
// 5. Processing Action Routes (BEFORE Outputting HTML Layouts)
// -------------------------------------------------------------------------
// معالجة الطلبات التي تستدعي إعادة توجيه (Redirect) قبل طباعة أي مخرجات
switch ($page) {
    case 'tickets-store':
        $ticketController->store();
        exit;

    case 'tickets-assign':
        $ticketController->assignEngineer();
        exit;

    case 'tickets-update-status':
        $ticketController->updateStatus();
        exit;
}

// -------------------------------------------------------------------------
// 6. Global Data Preparation & Layout Header
// -------------------------------------------------------------------------
$data = $ticketController->index();
$tickets = $data['tickets'] ?? [];

include __DIR__ . '/../views/layouts/header.php';

// -------------------------------------------------------------------------
// 7. Centralized View Rendering & Dispatcher
// -------------------------------------------------------------------------
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

    case 'users-store':
        $authController->storeUser();
        break;

    case 'users-create':
        $authController->showRegisterForm();
        break;

    case 'users-list':
        $users = $userRepo->getAllUsers();
        include __DIR__ . '/../views/users/list.php';
        break;
    case 'tickets-show':    
        $ticketId = isset($_GET['id']) ? filter_var($_GET['id'], FILTER_VALIDATE_INT) : 0;
        $ticket = $ticketController->show($ticketId);
        $engineers = $userRepo->getAllEngineers();

        include __DIR__ . '/../views/tickets/show.php';
        break;

    default:
        http_response_code(404);
        echo '<div class="table-section" style="padding: 40px; text-align: center; color: var(--critical-color);">'
           . '<h3>الصفحة المطلوبة غير موجودة (404)</h3>'
           . '<p style="color: var(--text-secondary); margin-top: 10px;">عذراً، المسار الذي تحاول الوصول إليه غير متوفر في النظام.</p>'
           . '</div>';
        break;
}

include __DIR__ . '/../views/layouts/footer.php';