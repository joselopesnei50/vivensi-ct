<?php
namespace App\Controllers;

class LandingController
{
    public function index(): void
    {
        // Serve a landing page standalone
        require ROOT . '/public/landing.php';
    }

    public function cadastroForm(): void
    {
        require ROOT . '/public/cadastro.php';
    }

    public function cadastroStore(): void
    {
        require ROOT . '/public/cadastro.php';
    }
}
