<?php
/**
 * NetPulse Portal - Tickets Index View
 * Swiss Design Minimalist Interface
 */

// استلام القيم المحددة للفلتر للحفاظ على حالتها في القوائم المنسدلة
$selectedStatus = $_GET['status'] ?? '';
$selectedPriority = $_GET['priority'] ?? '';
$tickets = $tickets ?? [];
?>

<!-- Data Table Section with Filters -->
<div class="table-section">
    <div class="filter-bar">
        <div class="filter-title">إدارة سجل التذاكر التشغيلية</div>
        
        <!-- Filter Form -->
        <form method="GET" class="filter-form">
            <input type="hidden" name="page" value="tickets">
            
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
                <a href="index.php?page=tickets" class="reset-link">إعادة تعيين</a>
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
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($tickets)): ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-secondary); padding: 30px;">
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
                        <td style="font-family: 'Inter', sans-serif; font-size: 13px;"><?php echo htmlspecialchars($ticket->createdAt, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="index.php?page=tickets-show&id=<?php echo $ticket->ticketId; ?>" style="color: var(--medium-color); text-decoration: none; font-weight: 500;">عرض التفاصيل</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>