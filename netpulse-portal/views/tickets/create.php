<div class="portal-card">
    <h2 style="margin-bottom: 24px; font-size: 20px; color: var(--accent-color); font-weight: 700;">إصدار تذكرة تشغيلية جديدة</h2>
    
    <form action="index.php?page=tickets-store" method="POST" class="form-grid">
        <div class="form-group">
            <label class="form-label">عنوان المشكلة أو العطل</label>
            <input type="text" name="title" required class="filter-select" style="width: 100%; padding: 12px;" placeholder="مثال: انقطاع الاتصال بخادم قاعدة البيانات الرئيسي">
        </div>

        <div class="form-group">
            <label class="form-label">التفاصيل التشغيلية</label>
            <textarea name="description" rows="5" required class="filter-select" style="width: 100%; padding: 12px; resize: vertical;" placeholder="اكتب تفاصيل التشخيص الفني..."></textarea>
        </div>

        <div class="form-row-2">
            <div class="form-group">
                <label class="form-label">مستوى الأولوية</label>
                <select name="priority" class="filter-select" style="width: 100%; padding: 10px;">
                    <option value="CRITICAL">CRITICAL (حرجة)</option>
                    <option value="HIGH">HIGH (عالية)</option>
                    <option value="MEDIUM" selected>MEDIUM (متوسطة)</option>
                    <option value="LOW">LOW (منخفضة)</option>
                </select>
            </div>
        </div>

        <div style="display: flex; gap: 12px; margin-top: 10px;">
            <button type="submit" class="filter-btn" style="padding: 12px 24px; font-size: 14px;">حفظ وإصدار التذكرة</button>
            <a href="index.php?page=dashboard" class="filter-btn" style="background-color: var(--text-secondary); text-decoration: none; padding: 12px 24px; font-size: 14px; display: inline-block;">إلغاء</a>
        </div>
    </form>
</div>