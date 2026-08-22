<?php
/**
 * NetPulse Portal - Main Operational Dashboard
 * Swiss Design Minimalist Interface
 */

require_once dirname(__DIR__) . '/src/Controllers/TicketController.php';

$controller = $controller ?? new TicketController();
$data = $controller->index();
$tickets = $data['tickets'] ?? [];

// Calculate quick metrics
$totalTickets = count($tickets);
$criticalCount = count(array_filter($tickets, fn($t) => $t->priority === 'CRITICAL'));
$openCount = count(array_filter($tickets, fn($t) => $t->status === 'OPEN'));
$resolvedCount = count(array_filter($tickets, fn($t) => $t->status === 'RESOLVED' || $t->status === 'CLOSED'));
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NetPulse Portal | لوحة إدارة التذاكر التشغيلية</title>
    <!-- Google Fonts: Inter & IBM Plex Sans Arabic -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- External Brand Stylesheet -->
    <link rel="stylesheet" href="assets/css/netpulse.css">
</head>
<body>

<div class="container">
    <!-- Header with Branding Mark -->
    <header>
        <div class="brand-wrapper">
            <div class="brand-logo-mark">NP</div>
            <div class="brand">
                <h1>NetPulse Portal</h1>
                <p>نظام إدارة الأعطال التشغيلية وتذاكر الدعم الفني الذكي</p>
            </div>
        </div>
        <div class="meta-badge">ENV: PRODUCTION</div>
    </header>

    <!-- Metrics Summary -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-title">إجمالي التذاكر</div>
            <div class="metric-value"><?php echo $totalTickets; ?></div>
        </div>
        <div class="metric-card">
            <div class="metric-title">أعطال حرجة (Critical)</div>
            <div class="metric-value" style="color: var(--critical-color);"><?php echo $criticalCount; ?></div>
        </div>
        <div class="metric-card">
            <div class="metric-title">تذاكر مفتوحة</div>
            <div class="metric-value" style="color: var(--high-color);"><?php echo $openCount; ?></div>
        </div>
        <div class="metric-card">
            <div class="metric-title">مكتملة / مغلقة</div>
            <div class="metric-value" style="color: var(--low-color);"><?php echo $resolvedCount; ?></div>
        </div>
    </div>

 <?php
// استلام القيم المحددة للفلتر للحفاظ على حالتها في القوائم المنسدلة
$selectedStatus = $_GET['status'] ?? '';
$selectedPriority = $_GET['priority'] ?? '';
?>

<!-- Data Table Section -->
<div class="table-section">
    <div class="filter-bar">
        <div class="filter-title">سجل التذاكر النشطة في النظام</div>
        
        <!-- Filter Form -->
        <form method="GET" class="filter-form">
            <select name="status" class="filter-select">
                <option value="">جميع الحالات</option>
                <option value="OPEN" <?php echo ($selectedStatus === 'OPEN') ? 'selected' : ''; ?>>OPEN (مفتوحة)</option>
                <option value="IN_PROGRESS" <?php echo ($selectedStatus === 'IN_PROGRESS') ? 'selected' : ''; ?>>IN_PROGRESS (قيد العمل)</option>
                <option value="RESOLVED" <?php echo ($selectedStatus === 'RESOLVED') ? 'selected' : ''; ?>>RESOLVED (محلولة)</option>
                <option value="CLOSED" <?php echo ($selectedStatus === 'CLOSED') ? 'selected' : ''; ?>>CLOSED (مغلقة)</option>
            </select>

            <select name="priority" class="filter-select">
                <option value="">جميع الأولويات</option>
                <option value="CRITICAL" <?php echo ($selectedPriority === 'CRITICAL') ? 'selected' : ''; ?>>CRITICAL (حرجة)</option>
                <option value="HIGH" <?php echo ($selectedPriority === 'HIGH') ? 'selected' : ''; ?>>HIGH (عالية)</option>
                <option value="MEDIUM" <?php echo ($selectedPriority === 'MEDIUM') ? 'selected' : ''; ?>>MEDIUM (متوسطة)</option>
                <option value="LOW" <?php echo ($selectedPriority === 'LOW') ? 'selected' : ''; ?>>LOW (منخفضة)</option>
            </select>

            <button type="submit" class="filter-btn">تصفية</button>

            <?php if (!empty($selectedStatus) || !empty($selectedPriority)): ?>
                <a href="index.php" class="reset-link">إعادة تعيين</a>
            <?php endif; ?>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>الرقم المرجعي</th>
                <th>عنوان المشكلة</th>
                <th>الأولوية</th>
                <th>الحالة</th>
                <th>المهندس المسؤول</th>
                <th>تاريخ الإنشاء</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tickets)): ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: var(--text-secondary); padding: 30px;">
                        لا توجد تذاكر مطابقة لخيارات البحث الحالية.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($tickets as $ticket): ?>
                    <tr>
                        <td><span class="ticket-num"><?php echo htmlspecialchars($ticket->ticketNumber); ?></span></td>
                        <td><strong><?php echo htmlspecialchars($ticket->title); ?></strong></td>
                        <td>
                            <?php 
                                $pClass = match($ticket->priority) {
                                    'CRITICAL' => 'badge-critical',
                                    'HIGH'     => 'badge-high',
                                    'MEDIUM'   => 'badge-medium',
                                    default    => 'badge-low'
                                };
                            ?>
                            <span class="badge <?php echo $pClass; ?>"><?php echo $ticket->priority; ?></span>
                        </td>
                        <td><span class="status-pill"><?php echo htmlspecialchars($ticket->status); ?></span></td>
                        <td><?php echo $ticket->assignedTo ? 'مهندس #' . $ticket->assignedTo : '<span style="color: #9ca3af;">غير مُعين</span>'; ?></td>
                        <td style="font-family: 'Inter', sans-serif; font-size: 13px;"><?php echo htmlspecialchars($ticket->createdAt); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
    <!-- Footer -->
    <footer>
        NetPulse Portal &copy; 2026 | Engineered with LOVE.
    </footer>
</div>

</body>
</html>