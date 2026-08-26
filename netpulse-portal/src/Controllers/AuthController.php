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

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: /login');
        exit;
    }
}