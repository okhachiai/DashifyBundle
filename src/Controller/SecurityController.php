<?php

namespace Dashify\DashifyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controller for handling security-related actions like login and logout.
 */
class SecurityController extends AbstractController
{
    /**
     * Handles the login page and authentication.
     */
    #[Route('/admin/login', name: 'dashify_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Redirect if user is already logged in
        if ($this->getUser()) {
            return $this->redirectToRoute('dashify_dashboard');
        }

        return $this->render('@Dashify/auth/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * Handles the logout action.
     * This method is intentionally empty as it will be intercepted by the security system.
     */
    #[Route('/admin/logout', name: 'dashify_logout', methods: ['GET'])]
    public function logout(): void
    {
        // This method is intentionally empty. The logout is handled by the security system.
    }
} 