<?php


class AuthController {
    private UserRepository $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    public function showLoginForm(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // إذا كان المستخدم مسجل الدخول مسبقاً، يتم توجيهه للوحة التحكم
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }
// استدعاء واجهة تسجيل الدخول في مكانها الصحيح داخل الدالة
require_once __DIR__ . '/../../views/auth/login.php';    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = $this->userRepository->findByUsername($username);

            if ($user && password_verify($password, $user['password_hash'])) {
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                header('Location: /dashboard');
                exit;
            } else {
                $error = "اسم المستخدم أو كلمة المرور غير صحيحة.";
                require_once __DIR__ . '/../../views/auth/login.php';
            }
        }
    }
/**
     * Displays the register user form (Admin only).
     */
    public function showRegisterForm(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // التحقق من أن المستخدم مسجل دخول وانه مدير (Admin)
        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'ADMIN') {
            header('Location: /index.php?page=dashboard');
            exit;
        }

        require_once __DIR__ . '/../../views/auth/register.php';
    }

    /**
     * Stores a new user created by the admin.
     */
    public function storeUser(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'ADMIN') {
            header('Location: /index.php?page=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'SUPPORT_ENGINEER';

            if (empty($username) || empty($email) || empty($password)) {
                $error = "جميع الحقول الأساسية مطلوبة.";
                require_once __DIR__ . '/../../views/auth/register.php';
                return;
            }

            try {
                $this->userRepository->createUser([
                    'username' => $username,
                    'email' => $email,
                    'password' => $password,
                    'role' => $role
                ]);

                header('Location: /index.php?page=users-list&success=created');
                exit;
            } catch (\Exception $e) {
                $error = "فشل إنشاء المستخدم: " . $e->getMessage();
                require_once __DIR__ . '/../../views/auth/register.php';
            }
        }
    }
    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: /login');
        exit;
    }
}