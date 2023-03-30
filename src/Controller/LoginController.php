<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    #[Route(path: '/api/login_check', name: 'api_login_check', methods: ['POST'])]
    public function check(): void
    {
    }
}
