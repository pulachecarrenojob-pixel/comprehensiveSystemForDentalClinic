<?php
class AuthController extends Controller {

    public function showLogin(): void {
        Auth::guest();
        $this->view('auth/login', ['layout' => 'layouts/auth', 'title' => 'Login']);
    }

    public function login(): void {
        Auth::guest();
        Auth::verifyCsrf();

        $email    = $this->sanitize($this->input('email'));
        $password = $this->input('password');

        if (empty($email) || empty($password)) {
            flash('error', 'Please fill in all fields.');
            $this->redirect('/login');
        }

        $db   = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND is_active = 1 LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            Auth::login($user);
            flash('success', 'Welcome back, ' . $user['name'] . '!');
            $this->redirect('/dashboard');
        } else {
            flash('error', 'Invalid email or password.');
            $this->redirect('/login');
        }
    }

    public function logout(): void {
        Auth::logout();
    }
}
