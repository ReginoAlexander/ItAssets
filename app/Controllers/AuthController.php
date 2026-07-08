<?php

require_once __DIR__ . '/../Models/User.php';


class AuthController{
    private $userModel;

    public function __construct(){
        $this->userModel = new User();
    }

    public function showLogin(){
        require_once __DIR__. '/../views/login.php';
    }

    public function login(){
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        $user = $this->userModel->login($username, $password);

        if(!$user) {
            $error = 'Usuario o contrasena incorrectos';
            require_once __DIR__ . '/../views/login.php';
            return;
        }

        session_start();
        $_SESSION['user'] = [
            'id' => $user['id'],
            'username' => $user['username'],
            'name' => $user['department'],
            'position' => $user['position'],

        ];

        header('Location: /ItAssets/public/main'); 
        exit;

    }

    public function logout(){
        session_start();
        session_destroy();
        header('Location: /login');
        exit;
    }
}



?>