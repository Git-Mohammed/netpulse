<?php
$tickets = $tickets ?? [];
$totalTickets = count($tickets);

// 1. إحصائيات الحالات (للرسم البياني الدائري)
$openCount = count(array_filter($tickets, fn($t) => $t->status === 'OPEN'));
$inProgressCount = count(array_filter($tickets, fn($t) => $t->status === 'IN_PROGRESS'));
$resolvedCount = count(array_filter($tickets, fn($t) => $t->status === 'RESOLVED'));
$closedCount = count(array_filter($tickets, fn($t) => $t->status === 'CLOSED'));
$completedCount = $resolvedCount + $closedCount;

// 2. إحصائيات الأولويات (للرسم البياني الشريطي)
$criticalCount = count(array_filter($tickets, fn($t) => $t->priority === 'CRITICAL'));
$highCount = count(array_filter($tickets, fn($t) => $t->priority === 'HIGH'));
$mediumCount = count(array_filter($tickets, fn($t) => $t->priority === 'MEDIUM'));
$lowCount = count(array_filter($tickets, fn($t) => $t->priority === 'LOW'));

$selectedStatus = $_GET['status'] ?? '';
$selectedPriority = $_GET['priority'] ?? '';
?>

<!-- استدعاء مكتبة Chart.js (يفضل وضعها في الـ Head في ملف header.php لكن يمكن تركها هنا للعمل مباشرة) -->

<!-- Metrics Summary (البطاقات العلوية) -->
<div class="metrics-grid">
    <div class="metric-card">
        <div class="metric-icon" style="color: var(--medium-color);"><i class="ph-bold ph-ticket"></i></div>
        <div>
            <div class="metric-title">إجمالي التذاكر</div>
            <div class="metric-value"><?php echo $totalTickets; ?></div>
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-icon" style="color: var(--critical-color);"><i class="ph-bold ph-warning-circle"></i></div>
        <div>
            <div class="metric-title">أعطال حرجة</div>
            <div class="metric-value" style="color: var(--critical-color);"><?php echo $criticalCount; ?></div>
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-icon" style="color: var(--high-color);"><i class="ph-bold ph-folder-open"></i></div>
        <div>
            <div class="metric-title">تذاكر مفتوحة</div>
            <div class="metric-value" style="color: var(--high-color);"><?php echo $openCount; ?></div>
        </div>
    </div>
    <div class="metric-card">
        <div class="metric-icon" style="color: var(--low-color);"><i class="ph-bold ph-check-circle"></i></div>
        <div>
            <div class="metric-title">مكتملة / مغلقة</div>
            <div class="metric-value" style="color: var(--low-color);"><?php echo $completedCount; ?></div>
        </div>
    </div>
</div>

<!-- Charts Section (قسم الرسوم البيانية الجديد) -->
<div class="charts-grid">
    <!-- Chart 1: Status Distribution -->
    <div class="portal-card chart-container">
        <h3 class="chart-title">توزيع التذاكر حسب الحالة</h3>
        <div style="position: relative; height: 250px; width: 100%; display: flex; justify-content: center;">
            <canvas id="statusChart"></canvas>
        </div>
    </div>
    
    <!-- Chart 2: Priority Breakdown -->
    <div class="portal-card chart-container">
        <h3 class="chart-title">تحليل مستويات الأولوية</h3>
        <div style="position: relative; height: 250px; width: 100%;">
            <canvas id="priorityChart"></canvas>
        </div>
    </div>
</div>

<!-- Data Table Section (سجل التذاكر) -->
<div class="table-section" style="margin-top: 30px;">
    <div class="filter-bar">
        <div class="filter-title">سجل التذاكر النشطة في النظام</div>
        
        <form method="GET" class="filter-form">
            <input type="hidden" name="page" value="dashboard">
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
                <th>الإجراءات</th>
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
                        <td><span class="ticket-num"><?php echo htmlspecialchars($ticket->ticketNumber, ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td><strong><?php echo htmlspecialchars($ticket->title, ENT_QUOTES, 'UTF-8'); ?></strong></td>
                        <td>
                            <?php 
                                $pClass = match($ticket->priority) {
                                    'CRITICAL' => 'badge-critical',
                                    'HIGH'     => 'badge-high',
                                    'MEDIUM'   => 'badge-medium',
                                    default    => 'badge-low'
                                };
                            ?>
                            <span class="badge <?php echo $pClass; ?>"><?php echo htmlspecialchars($ticket->priority, ENT_QUOTES, 'UTF-8'); ?></span>
                        </td>
                        <td><span class="status-pill"><?php echo htmlspecialchars($ticket->status, ENT_QUOTES, 'UTF-8'); ?></span></td>
                        <td><?php echo $ticket->assignedTo ? 'مهندس #' . htmlspecialchars($ticket->assignedTo, ENT_QUOTES, 'UTF-8') : '<span style="color: #9ca3af;">غير مُعين</span>'; ?></td>
                        <td>
                            <a href="index.php?page=tickets-show&id=<?php echo $ticket->ticketId; ?>" style="color: var(--medium-color); text-decoration: none; font-weight: 500;">عرض التفاصيل</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- JavaScript لتهيئة الرسوم البيانية (Chart.js) -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    // 1. تهيئة الرسم البياني لحالة التذاكر (Doughnut Chart)
    const ctxStatus = document.getElementById('statusChart').getContext('2d');
    new Chart(ctxStatus, {
        type: 'doughnut',
        data: {
            labels: ['مفتوحة', 'قيد العمل', 'محلولة', 'مغلقة'],
            datasets: [{
                data: [<?php echo $openCount; ?>, <?php echo $inProgressCount; ?>, <?php echo $resolvedCount; ?>, <?php echo $closedCount; ?>],
                backgroundColor: [
                    '#d97706', // High/Open color
                    '#2563eb', // Medium/InProgress color
                    '#059669', // Low/Resolved color
                    '#64748b'  // Closed/Neutral color
                ],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%', // لجعل الحلقة أنحف وأكثر حداثة
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { font: { family: "'IBM Plex Sans Arabic', sans-serif", size: 12 } }
                }
            }
        }
    });

    // 2. تهيئة الرسم البياني للأولويات (Bar Chart)
    const ctxPriority = document.getElementById('priorityChart').getContext('2d');
    new Chart(ctxPriority, {
        type: 'bar',
        data: {
            labels: ['حرجة', 'عالية', 'متوسطة', 'منخفضة'],
            datasets: [{
                label: 'عدد التذاكر',
                data: [<?php echo $criticalCount; ?>, <?php echo $highCount; ?>, <?php echo $mediumCount; ?>, <?php echo $lowCount; ?>],
                backgroundColor: [
                    '#dc2626', // Critical
                    '#d97706', // High
                    '#2563eb', // Medium
                    '#059669'  // Low
                ],
                borderRadius: 6, // حواف دائرية للأعمدة
                borderSkipped: false,
                barThickness: 30 // عرض ثابت للأعمدة
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false } // إخفاء المفتاح لعدم الحاجة إليه هنا
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, font: { family: "'Inter', sans-serif" } },
                    grid: { color: '#f3f4f6', drawBorder: false }
                },
                x: {
                    grid: { display: false, drawBorder: false },
                    ticks: { font: { family: "'IBM Plex Sans Arabic', sans-serif" } }
                }
            }
        }
    });
});
</script>