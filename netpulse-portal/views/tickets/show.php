<?php
$ticket = $ticket ?? null;
?>

<div class="portal-card">
    <?php if (!$ticket): ?>
        <p style="text-align: center; color: var(--critical-color); padding: 40px;">التذكرة المطلوبة غير موجودة أو تم حذفها.</p>
    <?php else: ?>
        <div class="ticket-detail-header">
            <div>
                <span class="ticket-num" style="font-size: 15px;"><?php echo htmlspecialchars($ticket->ticketNumber, ENT_QUOTES, 'UTF-8'); ?></span>
                <h2 style="margin-top: 6px; font-size: 22px; color: var(--accent-color);"><?php echo htmlspecialchars($ticket->title, ENT_QUOTES, 'UTF-8'); ?></h2>
            </div>
            <div>
                <span class="badge badge-<?php echo strtolower($ticket->priority); ?>" style="font-size: 13px; padding: 6px 12px;"><?php echo htmlspecialchars($ticket->priority, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>
        </div>

        <div class="form-grid">
            <div>
                <h4 style="font-size: 12px; color: var(--text-secondary); text-transform: uppercase; margin-bottom: 8px; letter-spacing: 0.5px;">وصف العطل</h4>
                <p style="background: var(--bg-color); padding: 16px; border-radius: 6px; border: 1px solid var(--border-color); font-size: 14px;">
                    <?php echo nl2br(htmlspecialchars($ticket->description, ENT_QUOTES, 'UTF-8')); ?>
                </p>
            </div>

            <div class="ticket-info-grid">
                <div>
                    <span style="font-size: 12px; color: var(--text-secondary); display: block; margin-bottom: 4px;">حالة التذكرة</span>
                    <strong><?php echo htmlspecialchars($ticket->status, ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
                <div>
                    <span style="font-size: 12px; color: var(--text-secondary); display: block; margin-bottom: 4px;">المهندس المسؤول</span>
                    <strong><?php echo $ticket->assignedTo ? 'مهندس #' . htmlspecialchars($ticket->assignedTo, ENT_QUOTES, 'UTF-8') : '<span style="color: #9ca3af;">غير مُعين</span>'; ?></strong>
                </div>
                <div>
                    <span style="font-size: 12px; color: var(--text-secondary); display: block; margin-bottom: 4px;">تاريخ الإنشاء</span>
                    <span style="font-family: var(--font-mono); font-size: 13px;"><?php echo htmlspecialchars($ticket->createdAt, ENT_QUOTES, 'UTF-8'); ?></span>
                </div>
            </div>
        </div>

        <!-- قسم تحديث حالة التذكرة -->
        <div style="margin-top: 32px; background: #f9fafb; padding: 24px; border-radius: 8px; border: 1px solid var(--border-color);">
            <h3 style="font-size: 16px; margin-bottom: 16px; color: var(--accent-color); font-weight: 600;">تحديث حالة التذكرة وسجل المراجعة</h3>
            
            <?php if (isset($_GET['success'])): ?>
                <div class="alert-box alert-success">تم تحديث حالة التذكرة وتدوين سجل التدقيق بنجاح.</div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert-box alert-error"><?php echo htmlspecialchars(urldecode($_GET['error']), ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>

            <form action="index.php?page=tickets-update-status" method="POST" class="form-grid">
                <input type="hidden" name="ticket_id" value="<?php echo $ticket->ticketId; ?>">
                
                <div class="form-row-2">
                    <div class="form-group">
                        <label class="form-label" style="font-size: 13px;">الحالة الجديدة</label>
                        <select name="status" class="filter-select" style="width: 100%; padding: 10px;">
                            <option value="OPEN" <?php echo ($ticket->status === 'OPEN') ? 'selected' : ''; ?>>OPEN (مفتوحة)</option>
                            <option value="IN_PROGRESS" <?php echo ($ticket->status === 'IN_PROGRESS') ? 'selected' : ''; ?>>IN_PROGRESS (قيد العمل)</option>
                            <option value="RESOLVED" <?php echo ($ticket->status === 'RESOLVED') ? 'selected' : ''; ?>>RESOLVED (محلولة)</option>
                            <option value="CLOSED" <?php echo ($ticket->status === 'CLOSED') ? 'selected' : ''; ?>>CLOSED (مغلقة)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" style="font-size: 13px;">سبب التغيير (Audit Note)</label>
                        <input type="text" name="note" class="filter-select" style="width: 100%; padding: 10px;" placeholder="اكتب ملاحظة تشغيلية تسجل في سجل المراجعة...">
                    </div>
                </div>

                <div>
                    <button type="submit" class="filter-btn" style="padding: 10px 20px;">تنفيذ التحديث وحفظ السجل</button>
                </div>
            </form>
        </div>

        <div style="margin-top: 24px;">
            <a href="index.php?page=dashboard" class="filter-btn" style="background-color: var(--text-secondary); text-decoration: none; display: inline-block;">العودة للقائمة</a>
        </div>
    <?php endif; ?>
</div>