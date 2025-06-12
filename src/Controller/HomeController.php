<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function index(): Response
    {
        // If user is logged in, redirect to tasks, otherwise to login
        if ($this->getUser()) {
            return $this->redirectToRoute('app_task_index');
        }

        return $this->redirectToRoute('app_login');
    }
}
