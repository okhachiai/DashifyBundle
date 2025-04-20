<?php

namespace Dashify\DashifyBundle\Security;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DashifySecurityManager
{
    private array $securityConfig;

    public function __construct(
        private readonly ParameterBagInterface $params,
        private readonly AuthorizationCheckerInterface $authChecker
    ) {
        $this->securityConfig = $this->params->get('dashify.config.security');
    }

    public function isLoginEnabled(): bool
    {
        return $this->securityConfig['enable_login'];
    }

    public function getLoginRoute(): string
    {
        return $this->securityConfig['login_route'];
    }

    public function getLogoutRoute(): string
    {
        return $this->securityConfig['logout_route'];
    }

    public function getSessionLifetime(): int
    {
        return $this->securityConfig['session_lifetime'];
    }

    public function canView(): bool
    {
        foreach ($this->securityConfig['roles']['view'] as $role) {
            if ($this->authChecker->isGranted($role)) {
                return true;
            }
        }
        return false;
    }

    public function canCreate(): bool
    {
        foreach ($this->securityConfig['roles']['create'] as $role) {
            if ($this->authChecker->isGranted($role)) {
                return true;
            }
        }
        return false;
    }

    public function canEdit(): bool
    {
        foreach ($this->securityConfig['roles']['edit'] as $role) {
            if ($this->authChecker->isGranted($role)) {
                return true;
            }
        }
        return false;
    }

    public function canDelete(): bool
    {
        foreach ($this->securityConfig['roles']['delete'] as $role) {
            if ($this->authChecker->isGranted($role)) {
                return true;
            }
        }
        return false;
    }
} 