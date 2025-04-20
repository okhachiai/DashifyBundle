<?php

namespace Dashify\DashifyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'dashify_dashboard')]
    public function index(): Response
    {
        return $this->render('@Dashify/dashboard/index.html.twig', [
            'title' => 'Dashboard',
        ]);
    }
}
