<?php

namespace Dashify\DashifyBundle\Security;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SecurityManager
{
    private array $config;
    private AuthorizationCheckerInterface $authChecker;

    public function __construct(array $config, AuthorizationCheckerInterface $authChecker)
    {
        $this->config = $config;
        $this->authChecker = $authChecker;
    }

    public function isLoginEnabled(): bool
    {
        return $this->config['enable_login'] ?? true;
    }

    public function getLoginRoute(): string
    {
        return $this->config['login_route'] ?? 'dashify_login';
    }

    public function getLogoutRoute(): string
    {
        return $this->config['logout_route'] ?? 'dashify_logout';
    }

    public function getLoginRedirectRoute(): string
    {
        return $this->config['login_redirect_route'] ?? 'dashify_dashboard';
    }

    public function getLogoutRedirectRoute(): string
    {
        return $this->config['logout_redirect_route'] ?? 'dashify_login';
    }

    public function getUserProvider(): string
    {
        return $this->config['user_provider'] ?? 'app.user_provider';
    }

    public function getPasswordHasher(): string
    {
        return $this->config['password_hasher'] ?? 'auto';
    }

    public function isRememberMeEnabled(): bool
    {
        return $this->config['remember_me'] ?? true;
    }

    public function getSessionLifetime(): int
    {
        return $this->config['session_lifetime'] ?? 3600;
    }

    public function getRoles(): array
    {
        return $this->config['roles'] ?? ['ROLE_DASHIFY_USER', 'ROLE_DASHIFY_ADMIN'];
    }

    public function canView(): bool
    {
        return $this->hasAnyRole($this->config['permissions']['view'] ?? ['ROLE_DASHIFY_USER']);
    }

    public function canCreate(): bool
    {
        return $this->hasAnyRole($this->config['permissions']['create'] ?? ['ROLE_DASHIFY_ADMIN']);
    }

    public function canEdit(): bool
    {
        return $this->hasAnyRole($this->config['permissions']['edit'] ?? ['ROLE_DASHIFY_ADMIN']);
    }

    public function canDelete(): bool
    {
        return $this->hasAnyRole($this->config['permissions']['delete'] ?? ['ROLE_DASHIFY_ADMIN']);
    }

    public function denyAccessUnlessCanView(): void
    {
        if (!$this->canView()) {
            throw new AccessDeniedException('Access Denied: Insufficient permissions to view this resource.');
        }
    }

    public function denyAccessUnlessCanCreate(): void
    {
        if (!$this->canCreate()) {
            throw new AccessDeniedException('Access Denied: Insufficient permissions to create this resource.');
        }
    }

    public function denyAccessUnlessCanEdit(): void
    {
        if (!$this->canEdit()) {
            throw new AccessDeniedException('Access Denied: Insufficient permissions to edit this resource.');
        }
    }

    public function denyAccessUnlessCanDelete(): void
    {
        if (!$this->canDelete()) {
            throw new AccessDeniedException('Access Denied: Insufficient permissions to delete this resource.');
        }
    }

    private function hasAnyRole(array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->authChecker->isGranted($role)) {
                return true;
            }
        }
        return false;
    }
} 