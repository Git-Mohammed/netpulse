<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قائمة المستخدمين - NetPulse NOC</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        :root {
            --bg-main: #f8fafc;
            --surface: #ffffff;
            --primary: #0284c7;
            --primary-hover: #0369a1;
            --text-main: #0f172a;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --radius: 12px;
            --success-bg: #f0fdf4;
            --success-text: #16a34a;
            --success-border: #dcfce7;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            min-height: 100vh;
            padding: 2rem;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .dashboard-container {
            width: 100%;
            max-width: 1000px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05), 0 8px 10px -6px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        /* رأس الصفحة */
        .page-header {
            padding: 2rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-icon {
            width: 48px;
            height: 48px;
            background: #e0f2fe;
            color: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            flex-shrink: 0;
        }

        .header-title h1 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.25rem;
        }

        .header-title p {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .btn-action {
            padding: 0.625rem 1.25rem;
            font-family: inherit;
            font-size: 0.9rem;
            font-weight: 600;
            color: #fff;
            background-color: var(--primary);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }

        .btn-action:hover {
            background-color: var(--primary-hover);
        }

        /* محتوى الصفحة */
        .page-body {
            padding: 2rem;
        }

        /* التنبيهات */
        .alert-success {
            background-color: var(--success-bg);
            color: var(--success-text);
            border: 1px solid var(--success-border);
            padding: 0.875rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* الجدول */
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border: 1px solid var(--border);
            border-radius: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: right;
            font-size: 0.9rem;
        }

        th {
            background-color: #f1f5f9;
            color: var(--text-main);
            font-weight: 600;
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 0.875rem 1rem;
            border-bottom: 1px solid var(--border);
            color: var(--text-main);
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background-color: #f8fafc;
        }

        /* شارات الصلاحيات (Badges) */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 6px;
        }

        .badge-admin {
            background-color: #fef3c7;
            color: #d97706;
        }

        .badge-engineer {
            background-color: #e0f2fe;
            color: #0369a1;
        }

        /* تذييل الصفحة */
        .page-footer {
            padding: 1rem 2rem;
            background-color: #f8fafc;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .back-link {
            font-size: 0.85rem;
            color: var(--text-muted);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            transition: color 0.2s;
        }

        .back-link:hover {
            color: var(--primary);
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        <!-- رأس الصفحة -->
        <div class="page-header">
            <div class="header-info">
                <div class="header-icon">
                    <i class="ph ph-users"></i>
                </div>
                <div class="header-title">
                    <h1>إدارة المستخدمين</h1>
                    <p>عرض ومتابعة طاقم العمل المسجلين - NetPulse NOC</p>
                </div>
            </div>
            <a href="index.php?page=users-create" class="btn-action">
                <i class="ph ph-user-plus" style="font-size: 18px;"></i>
                <span>مستخدم جديد</span>
            </a>
        </div>

        <div class="page-body">
            <!-- رسالة النجاح في حال تمت الإضافة -->
            <?php if (isset($_GET['success']) && $_GET['success'] == 'created'): ?>
                <div class="alert-success">
                    <i class="ph ph-check-circle" style="font-size: 18px;"></i>
                    <span>تم إنشاء المستخدم الجديد بنجاح!</span>
                </div>
            <?php endif; ?>

            <!-- جدول المستخدمين -->
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th># ID</th>
                            <th>اسم المستخدم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الصلاحية (Role)</th>
                            <th>تاريخ الإنشاء</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- مثال برمجي لتعبئة البيانات من قاعدة البيانات باستخدام PHP -->
                        <?php if (!empty($users)): ?>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= htmlspecialchars($user['user_id']) ?></td>
                                    <td><strong><?= htmlspecialchars($user['username']) ?></strong></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>
                                        <?php if ($user['role'] === 'ADMIN'): ?>
                                            <span class="badge badge-admin">مدير النظام (Admin)</span>
                                        <?php else: ?>
                                            <span class="badge badge-engineer">مهندس دعم (Engineer)</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <!-- بيانات تجريبية مطابقة لجدولك في حال عدم توفر متغير الـ users -->
                            <tr>
                                <td>1</td>
                                <td><strong>admin_fares</strong></td>
                                <td>fares@netpulse.local</td>
                                <td><span class="badge badge-admin">مدير النظام (Admin)</span></td>
                                <td>2026-01-10 08:00:00</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td><strong>eng_sara</strong></td>
                                <td>sara@netpulse.local</td>
                                <td><span class="badge badge-engineer">مهندس دعم (Engineer)</span></td>
                                <td>2026-01-12 09:30:00</td>
                            </tr>
                            <tr>
                                <td>7</td>
                                <td><strong>admin</strong></td>
                                <td>admin@netpulse.local</td>
                                <td><span class="badge badge-admin">مدير النظام (Admin)</span></td>
                                <td>2026-08-27 01:00:54</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- تذييل الصفحة -->
        <div class="page-footer">
            <a href="index.php?page=dashboard" class="back-link">
                <i class="ph ph-arrow-right"></i>
                العودة إلى لوحة التحكم الرئيسية
            </a>
            <span style="font-size: 0.8rem; color: var(--text-muted);">NetPulse NOC System v2.6</span>
        </div>
    </div>

</body>
</html>