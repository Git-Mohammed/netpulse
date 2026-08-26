<?php
$ticket = $ticket ?? null;
?>

<style>
    /* التنسيقات العصرية المدمجة */
    .modern-ticket-wrapper {
        direction: rtl;
        max-width: 950px;
        margin: 0 auto;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        color: #374151;
    }
    .modern-card {
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border: 1px solid #e5e7eb;
        padding: 24px;
        margin-bottom: 24px;
    }
    .ticket-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        border-bottom: 1px solid #f3f4f6;
        padding-bottom: 16px;
        margin-bottom: 20px;
    }
    .ticket-title-group h2 {
        margin: 8px 0 0 0;
        font-size: 24px;
        font-weight: 700;
        color: #111827;
    }
    .ticket-id {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f3f4f6;
        color: #4b5563;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .meta-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 20px;
    }
    .meta-item {
        background: #f9fafb;
        padding: 16px;
        border-radius: 8px;
        border: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .meta-icon {
        width: 40px;
        height: 40px;
        background: #eff6ff;
        color: #3b82f6;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .meta-details span {
        display: block;
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 4px;
    }
    .meta-details strong {
        color: #111827;
        font-size: 14px;
    }
    .desc-box {
        background: #f9fafb;
        border-right: 4px solid #3b82f6;
        padding: 16px 20px;
        border-radius: 8px;
        font-size: 15px;
        line-height: 1.6;
        color: #4b5563;
        margin-bottom: 24px;
    }
    .actions-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 24px;
    }
    @media (max-width: 768px) {
        .actions-grid { grid-template-columns: 1fr; }
        .ticket-header { flex-direction: column; gap: 12px; }
    }
    .form-group {
        margin-bottom: 16px;
    }
    .modern-label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }
    .modern-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        transition: all 0.2s;
        box-sizing: border-box;
    }
    .modern-input:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .modern-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }
    .btn-primary { background: #3b82f6; color: #fff; }
    .btn-primary:hover { background: #2563eb; }
    .btn-secondary { background: #f3f4f6; color: #4b5563; text-decoration: none; }
    .btn-secondary:hover { background: #e5e7eb; }
    .btn-dark { background: #1f2937; color: #fff; }
    .btn-dark:hover { background: #111827; }
    
    /* Alerts */
    .modern-alert {
        padding: 12px 16px;
        border-radius: 6px;
        margin-bottom: 20px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .alert-success { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }
    .alert-error { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
</style>

<div class="modern-ticket-wrapper">
    
    <?php if (!$ticket): ?>
        <div class="modern-card" style="text-align: center; padding: 60px 20px;">
            <svg style="width: 64px; height: 64px; color: #ef4444; margin: 0 auto 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            <h3 style="font-size: 18px; color: #111827;">التذكرة غير موجودة</h3>
            <p style="color: #6b7280; margin-top: 8px;">التذكرة المطلوبة غير موجودة في النظام أو تم حذفها مسبقاً.</p>
            <a href="index.php?page=dashboard" class="modern-btn btn-secondary" style="margin-top: 20px;">العودة للرئيسية</a>
        </div>
    <?php else: ?>

        <!-- كارت التفاصيل الأساسية -->
        <div class="modern-card">
            <div class="ticket-header">
                <div class="ticket-title-group">
                    <span class="ticket-id">
                        <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path></svg>
                        <?php echo htmlspecialchars($ticket->ticketNumber, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                    <h2><?php echo htmlspecialchars($ticket->title, ENT_QUOTES, 'UTF-8'); ?></h2>
                </div>
                <div>
                    <!-- احتفظت بـ badge class الخاصة بك إذا كانت مسؤولة عن الألوان -->
                    <span class="badge badge-<?php echo strtolower($ticket->priority); ?>" style="display: inline-block; padding: 6px 16px; border-radius: 50px; font-size: 13px; font-weight: 600; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca;">
                        <?php echo htmlspecialchars($ticket->priority, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                </div>
            </div>

            <h4 style="font-size: 14px; color: #374151; margin-bottom: 12px; font-weight: 600;">وصف العطل / المشكلة</h4>
            <div class="desc-box">
                <?php echo nl2br(htmlspecialchars($ticket->description, ENT_QUOTES, 'UTF-8')); ?>
            </div>

            <div class="meta-grid">
                <div class="meta-item">
                    <div class="meta-icon">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="meta-details">
                        <span>حالة التذكرة</span>
                        <strong><?php echo htmlspecialchars($ticket->status, ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                </div>

                <div class="meta-item">
                    <div class="meta-icon" style="background: #f3e8ff; color: #9333ea;">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div class="meta-details">
                        <span>المهندس المسؤول</span>
                        <?php if ($ticket->assignedEngineer && !empty($ticket->assignedEngineer->username)): ?>
                            <strong><?php echo htmlspecialchars($ticket->assignedEngineer->username, ENT_QUOTES, 'UTF-8'); ?></strong>
                        <?php else: ?>
                            <strong style="color: #9ca3af; font-style: italic;">غير مُعين</strong>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="meta-item">
                    <div class="meta-icon" style="background: #e0f2fe; color: #0284c7;">
                        <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div class="meta-details">
                        <span>تاريخ الإنشاء</span>
                        <strong style="direction: ltr; display: inline-block;"><?php echo htmlspecialchars($ticket->createdAt, ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- التنبيهات -->
        <?php if (isset($_GET['success'])): ?>
            <div class="modern-alert alert-success">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                تم تحديث حالة التذكرة بنجاح.
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="modern-alert alert-error">
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <?php echo htmlspecialchars(urldecode($_GET['error']), ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <!-- كارت الإدارة والعمليات -->
        <div class="actions-grid">
            
            <!-- تحديث الحالة -->
            <div class="modern-card" style="margin-bottom: 0;">
                <h3 style="font-size: 16px; margin-top: 0; margin-bottom: 16px; color: #111827; border-bottom: 1px solid #f3f4f6; padding-bottom: 12px;">تحديث الحالة والمراجعة</h3>
                <form action="index.php?page=tickets-update-status" method="POST">
                    <input type="hidden" name="ticket_id" value="<?php echo $ticket->ticketId; ?>">
                    
                    <div class="form-group">
                        <label class="modern-label">الحالة الجديدة</label>
                        <select name="status" class="modern-input">
                            <option value="OPEN" <?php echo ($ticket->status === 'OPEN') ? 'selected' : ''; ?>>OPEN (مفتوحة)</option>
                            <option value="IN_PROGRESS" <?php echo ($ticket->status === 'IN_PROGRESS') ? 'selected' : ''; ?>>IN_PROGRESS (قيد العمل)</option>
                            <option value="RESOLVED" <?php echo ($ticket->status === 'RESOLVED') ? 'selected' : ''; ?>>RESOLVED (محلولة)</option>
                            <option value="CLOSED" <?php echo ($ticket->status === 'CLOSED') ? 'selected' : ''; ?>>CLOSED (مغلقة)</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label class="modern-label">سبب التغيير (Audit Note)</label>
                        <input type="text" name="note" class="modern-input" placeholder="اكتب ملاحظة تشغيلية للتسجيل...">
                    </div>

                    <button type="submit" class="modern-btn btn-primary" style="width: 100%;">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        تنفيذ وحفظ السجل
                    </button>
                </form>
            </div>

            <!-- تعيين المهندس -->
            <div class="modern-card" style="margin-bottom: 0;">
                <h3 style="font-size: 16px; margin-top: 0; margin-bottom: 16px; color: #111827; border-bottom: 1px solid #f3f4f6; padding-bottom: 12px;">إدارة التعيين</h3>
                <form action="index.php?page=tickets-assign" method="POST">
                    <input type="hidden" name="ticket_id" value="<?php echo $ticket->ticketId; ?>">
                    
                    <div class="form-group">
                        <label class="modern-label">المهندس المسؤول</label>
                        <select name="assigned_to" class="modern-input">
                            <option value="">-- اختر المهندس --</option>
                            <?php 
                                $engineers = $engineers ?? []; 
                                echo $engineers;
                                foreach ($engineers as $engineer): 
                            ?>
<option value="<?php echo $engineer->userId; ?>" <?php echo (isset($ticket->assignedTo) && $ticket->assignedTo === $engineer->userId) ? 'selected' : ''; ?>>                                    <?php echo htmlspecialchars($engineer->username, ENT_QUOTES, 'UTF-8'); ?> (<?php echo $engineer->role; ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="modern-btn btn-dark" style="width: 100%; margin-top: auto;">
                        <svg style="width: 18px; height: 18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                        تحديث المسؤول
                    </button>
                </form>
            </div>

        </div>

        <div style="margin-top: 24px; text-align: right;">
            <a href="index.php?page=dashboard" class="modern-btn btn-secondary">
                <svg style="width: 18px; height: 18px; transform: rotate(180deg);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                العودة للقائمة
            </a>
        </div>

    <?php endif; ?>
</div>