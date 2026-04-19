<?php
namespace App\Controllers;

use App\Core\Auth;
use App\Core\Flash;
use App\Core\Request;
use App\Core\View;

class AuthController
{
    public function loginForm(): void
    {
        if (Auth::check()) {
            Request::redirect(url('/dashboard'));
        }
        View::render('auth/login', [], 'auth');
    }

    public function login(): void
    {
        if (!Request::verifyCsrf()) {
            Flash::error('Token de segurança inválido.');
            Request::redirect(url('/login'));
        }

        $email    = filter_var(Request::post('email', ''), FILTER_SANITIZE_EMAIL);
        $password = Request::post('password', '');

        if (empty($email) || empty($password)) {
            Flash::error('Preencha todos os campos.');
            Request::redirect(url('/login'));
        }

        if (Auth::isLockedOut()) {
            Flash::error('Muitas tentativas incorretas. Aguarde 5 minutos antes de tentar novamente.');
            Request::redirect(url('/login'));
        }

        if (Auth::attempt($email, $password)) {
            Flash::success('Bem-vindo(a) ao VivensiCT!');
            Request::redirect(url('/dashboard'));
        } else {
            Flash::error('E-mail ou senha incorretos.');
            Request::redirect(url('/login'));
        }
    }

    public function logout(): void
    {
        Auth::logout();
        Request::redirect(url('/login'));
    }
}
