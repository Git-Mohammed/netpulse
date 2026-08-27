<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - NetPulse NOC</title>
    
    <!-- خطوط IBM Plex Sans Arabic -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/assets/css/netpulse.css">

    <style>
        :root {
            --noc-bg: #0d0f12;
            --noc-primary: #3b82f6;
            --noc-primary-hover: #2563eb;
            --noc-text-main: #f8fafc;
            --noc-text-muted: #94a3b8;
            --noc-border: rgba(255, 255, 255, 0.08);
            --noc-glass-bg: rgba(20, 24, 30, 0.6);
            --noc-input-bg: rgba(15, 18, 23, 0.8);
        }

        body {
            margin: 0;
            font-family: 'IBM Plex Sans Arabic', sans-serif;
            background-color: var(--noc-bg);
            /* خلفية بتدرج شبكي خفيف لتناسب طابع الشبكات */
            background-image: 
                radial-gradient(at 0% 0%, rgba(59, 130, 246, 0.15) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(59, 130, 246, 0.05) 0px, transparent 50%);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: var(--noc-text-main);
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 2rem;
        }

        /* Glassmorphism Card */
        .glass-card {
            background: var(--noc-glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--noc-border);
            border-radius: 16px;
            padding: 3rem 2.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .brand-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }

        .brand-title {
            font-size: 1.75rem;
            font-weight: 700;
            letter-spacing: 1px;
            margin: 0 0 0.5rem 0;
            background: linear-gradient(to left, #fff, #93c5fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .brand-subtitle {
            font-size: 0.875rem;
            color: var(--noc-text-muted);
            font-weight: 400;
            margin: 0;
        }

        /* Minimalist Form Controls */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--noc-text-muted);
        }

        .form-control {
            width: 100%;
            padding: 0.875rem 1rem;
            font-family: inherit;
            font-size: 0.95rem;
            color: var(--noc-text-main);
            background: var(--noc-input-bg);
            border: 1px solid var(--noc-border);
            border-radius: 8px;
            outline: none;
            transition: all 0.2s ease-in-out;
            box-sizing: border-box;
        }

        .form-control:focus {
            border-color: var(--noc-primary);
            background: rgba(15, 18, 23, 1);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        }

        .btn-primary {
            width: 100%;
            padding: 0.875rem;
            font-family: inherit;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            background-color: var(--noc-primary);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s ease;
            margin-top: 1rem;
        }

        .btn-primary:hover {
            background-color: var(--noc-primary-hover);
        }

        /* Alert Styling */
        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.2);
            padding: 0.875rem;
            border-radius: 8px;
            font-size: 0.875rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>

    <div class="login-wrapper">
        <div class="glass-card">
            <div class="brand-header">
                <h1 class="brand-title">NetPulse NOC</h1>
                <p class="brand-subtitle">بوابة إدارة العمليات والشبكات المركزية</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert-danger" role="alert">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <!-- تم تعديل مسار action ليتوافق مع الروتر -->
            <form action="index.php?page=login-submit" method="POST">
                <div class="form-group">
                    <label for="username" class="form-label">اسم المستخدم</label>
                    <input type="text" class="form-control" id="username" name="username" required autocomplete="off" spellcheck="false" autofocus>
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>

                <button type="submit" class="btn-primary">دخول النظام</button>
            </form>
        </div>
    </div>

</body>
</html>