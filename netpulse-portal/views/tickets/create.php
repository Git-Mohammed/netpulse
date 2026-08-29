<?php 
    // تأمين قراءة الصلاحية وتحويلها إلى أحرف كبيرة لضمان نجاح الشرط دائماً
    $currentUserRole = isset($_SESSION['role']) ? strtoupper($_SESSION['role']) : 'GUEST';
?>

<div class="portal-card" style="max-width: 800px; margin: 0 auto; padding: 30px; background: #fff; border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.05);">
    
    <div style="border-bottom: 2px solid var(--accent-color, #e2e8f0); padding-bottom: 15px; margin-bottom: 25px;">
        <h2 style="margin: 0; font-size: 22px; color: var(--accent-color, #1e293b); font-weight: 700;">
            <i class="fas fa-ticket-alt" style="margin-left: 8px;"></i> إصدار تذكرة تشغيلية جديدة
        </h2>
        <p style="margin: 5px 0 0 0; font-size: 14px; color: #64748b;">الرجاء تعبئة تفاصيل العطل الفني بدقة لضمان سرعة المعالجة.</p>
    </div>
    
    <form action="index.php?page=tickets-store" method="POST" class="form-grid">
        
        <div class="form-group" style="margin-bottom: 20px;">
            <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">عنوان المشكلة أو العطل <span style="color: red;">*</span></label>
            <input type="text" name="title" required class="filter-select" 
                   style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px;" 
                   placeholder="مثال: انقطاع الاتصال بخادم قاعدة البيانات الرئيسي">
        </div>

        <div class="form-group" style="margin-bottom: 20px;">
            <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">التفاصيل التشغيلية <span style="color: red;">*</span></label>
            <textarea name="description" rows="5" required class="filter-select" 
                      style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; resize: vertical; min-height: 120px;" 
                      placeholder="اكتب تفاصيل التشخيص الفني، رموز الخطأ (إن وجدت)، والأنظمة المتأثرة..."></textarea>
        </div>

        <div class="form-row-2" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
            
            <!-- حقل الأولوية (يظهر للجميع) -->
            <div class="form-group">
                <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">مستوى الأولوية</label>
                <select name="priority" class="filter-select" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; background-color: #f8fafc;">
                    <option value="CRITICAL">🚨 CRITICAL (حرجة)</option>
                    <option value="HIGH">🔴 HIGH (عالية)</option>
                    <option value="MEDIUM" selected>🟠 MEDIUM (متوسطة)</option>
                    <option value="LOW">🟢 LOW (منخفضة)</option>
                </select>
            </div>

            <!-- ===== حقل تعيين المهندس (يظهر للمدير فقط) ===== -->
            <?php if ($currentUserRole === 'ADMIN'): ?>
                <div class="form-group">
                    <label class="form-label" style="display: block; margin-bottom: 8px; font-weight: 600; color: #334155;">تعيين إلى المهندس المسؤول</label>
                    <select name="assigned_to" class="filter-select" style="width: 100%; padding: 12px 15px; border: 1px solid #cbd5e1; border-radius: 8px; font-size: 15px; background-color: #f8fafc;">
                        <option value="">-- يتم الاختيار التلقائي لاحقاً --</option>
                        <?php 
                            $engineers = $engineers ?? []; 
                            foreach ($engineers as $engineer): 
                        ?>
                            <option value="<?php echo htmlspecialchars((string)$engineer->userId, ENT_QUOTES, 'UTF-8'); ?>">
                                <?php echo htmlspecialchars($engineer->username, ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($engineer->role, ENT_QUOTES, 'UTF-8'); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php else: ?>
                <!-- رسالة توضيحية تظهر للمهندس -->
                <div class="form-group" style="display: flex; align-items: center; background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 8px; padding: 12px 15px;">
                    <p style="margin: 0; color: #1e3a8a; font-size: 14px; font-weight: 500;">
                        ℹ️ سيتم إسناد هذه التذكرة إليك تلقائياً كمهندس مسؤول.
                    </p>
                </div>
            <?php endif; ?>
            <!-- ========================================================== -->

        </div>

        <!-- الأزرار -->
        <div style="display: flex; gap: 15px; margin-top: 10px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
            <button type="submit" class="filter-btn" 
                    style="background-color: #2563eb; color: white; border: none; padding: 12px 30px; font-size: 16px; font-weight: 600; border-radius: 8px; cursor: pointer; transition: background-color 0.2s;">
                حفظ وإصدار التذكرة
            </button>
            <a href="index.php?page=dashboard" class="filter-btn" 
               style="background-color: #e2e8f0; color: #475569; text-decoration: none; padding: 12px 30px; font-size: 16px; font-weight: 600; border-radius: 8px; text-align: center; transition: background-color 0.2s;">
                إلغاء الرجوع
            </a>
        </div>
    </form>
</div>