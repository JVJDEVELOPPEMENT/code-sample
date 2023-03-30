<?php

declare(strict_types=1);

namespace App\Controller\Admin\Home;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class HomeController extends AbstractController
{
    #[Route('/home', name: 'admin_home')]
    public function admin_home(): Response
    {
        return $this->render("admin/home/index.html.twig");
    }
}