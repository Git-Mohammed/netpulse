<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة مستخدم جديد - NetPulse NOC</title>
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
            --input-bg: #f8fafc;
            --radius: 12px;
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
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1.5rem;
        }

        .auth-container {
            width: 100%;
            max-width: 520px;
            margin: 0 auto;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.05), 0 8px 10px -6px rgba(15, 23, 42, 0.05);
            overflow: hidden;
        }

        .auth-header {
            padding: 2rem 2rem 1.5rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .auth-icon {
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

        .auth-title-group h1 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--text-main);
            margin-bottom: 0.25rem;
        }

        .auth-title-group p {
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        .auth-body {
            padding: 2rem;
        }

        .alert {
            background-color: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
            padding: 0.875rem 1rem;
            border-radius: 8px;
            font-size: 0.875rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
        }

        @media (min-width: 640px) {
            .form-grid.two-cols {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-label {
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .input-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrapper > i {
            position: absolute;
            right: 1rem;
            color: var(--text-muted);
            font-size: 18px;
            pointer-events: none;
        }

        .form-control, .form-select {
            width: 100%;
            padding: 0.75rem 2.75rem 0.75rem 1rem;
            font-family: inherit;
            font-size: 0.925rem;
            color: var(--text-main);
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 8px;
            outline: none;
            transition: all 0.2s ease;
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748b' viewBox='0 0 256 256'%3E%3Cpath d='M213.66,101.66l-80,80a8,8,0,0,1-11.32,0l-80-80A8,8,0,0,1,53.66,90.34L128,164.69l74.34-74.35a8,8,0,0,1,11.32,11.32Z'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: left 1rem center;
            padding-right: 2.75rem;
            padding-left: 2.5rem;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary);
            background: var(--surface);
            box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.12);
        }

        .btn-submit {
            width: 100%;
            padding: 0.875rem;
            font-family: inherit;
            font-size: 0.95rem;
            font-weight: 600;
            color: #fff;
            background-color: var(--primary);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        .btn-submit:hover {
            background-color: var(--primary-hover);
        }

        .btn-submit:active {
            transform: scale(0.99);
        }

        .auth-footer {
            padding: 1rem 2rem;
            background-color: #f8fafc;
            border-top: 1px solid var(--border);
            text-align: center;
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

    <div class="auth-container">
        <!-- رأس البطاقة -->
        <div class="auth-header">
            <div class="auth-icon">
                <i class="ph ph-user-plus"></i>
            </div>
            <div class="auth-title-group">
                <h1>إضافة مستخدم جديد</h1>
                <p>إدارة الصلاحيات وحسابات طاقم العمل - NetPulse NOC</p>
            </div>
        </div>

        <div class="auth-body">
            <?php if (!empty($error)): ?>
                <div class="alert">
                    <i class="ph ph-warning-circle" style="font-size: 18px;"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <form action="index.php?page=users-store" method="POST">
                <div class="form-grid">
                    <!-- اسم المستخدم -->
                    <div class="form-group">
                        <label class="form-label">اسم المستخدم</label>
                        <div class="input-wrapper">
                            <i class="ph ph-user"></i>
                            <input type="text" class="form-control" name="username" placeholder="مثال: ahmad_noc" required autocomplete="off">
                        </div>
                    </div>

                    <!-- البريد الإلكتروني -->
                    <div class="form-group">
                        <label class="form-label">البريد الإلكتروني</label>
                        <div class="input-wrapper">
                            <i class="ph ph-envelope"></i>
                            <input type="email" class="form-control" name="email" placeholder="name@netpulse.com" required autocomplete="off">
                        </div>
                    </div>
                </div>

                <div class="form-grid" style="margin-top: 1.25rem;">
                    <!-- كلمة المرور -->
                    <div class="form-group">
                        <label class="form-label">كلمة المرور</label>
                        <div class="input-wrapper">
                            <i class="ph ph-lock"></i>
                            <input type="password" class="form-control" name="password" placeholder="••••••••" required>
                        </div>
                    </div>

                    <!-- تأكيد كلمة المرور -->
                    <div class="form-group">
                        <label class="form-label">تأكيد كلمة المرور</label>
                        <div class="input-wrapper">
                            <i class="ph ph-lock-key"></i>
                            <input type="password" class="form-control" name="confirm_password" placeholder="••••••••" required>
                        </div>
                    </div>
                </div>

                <!-- الصلاحية -->
                <div class="form-group" style="margin-top: 1.25rem;">
                    <label class="form-label">صلاحية الحساب (Role)</label>
                    <div class="input-wrapper">
                        <select class="form-select" name="role">
            <option value="ENGINEER">مهندس دعم (Engineer)</option>
            <option value="ADMIN">مدير النظام (Admin)</option>   
                             </select>
                        <i class="ph ph-shield-check"></i>
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="ph-bold ph-check"></i>
                    <span>حفظ وإنشاء المستخدم</span>
                </button>
            </form>
        </div>

        <div class="auth-footer">
            <a href="index.php?page=dashboard" class="back-link">
                <i class="ph ph-arrow-right"></i>
                العودة إلى لوحة التحكم الرئيسية
            </a>
        </div>
    </div>

</body>
</html>