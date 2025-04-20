# DashifyBundle

Un bundle Symfony moderne pour créer des interfaces d'administration.

## Installation

1. Installer le bundle via Composer :
```bash
composer require okhachiai/dashify-bundle
```

2. Activer le bundle dans `config/bundles.php` :
```php
return [
    // ...
    Dashify\DashifyBundle\DashifyBundle::class => ['all' => true],
];
```

3. Configurer les routes dans `config/routes.yaml` :
```yaml
dashify:
    resource: '@DashifyBundle/Resources/config/routes/dashify.yaml'
    type: yaml
```

4. Configurer le bundle dans `config/packages/dashify.yaml` :
```yaml
dashify:
    title: 'Mon Admin'
    security:
        user_class: App\Entity\User
        user_identifier_property: email
        roles: ['ROLE_ADMIN']
        remember_me: true
        session_lifetime: 3600
```

5. Nettoyer le cache :
```bash
php bin/console cache:clear
```

## Configuration complète

Voici toutes les options de configuration disponibles :

```yaml
dashify:
    title: 'Mon Admin'                    # Titre de l'interface d'administration
    security:
        user_class: App\Entity\User       # Classe de l'entité utilisateur
        user_identifier_property: email    # Propriété utilisée pour l'identification (email, username, etc.)
        user_provider: 'security.user.provider.concrete.app_user_provider'  # (Optionnel) Service provider d'utilisateur
        roles: ['ROLE_ADMIN']             # Rôles requis pour accéder à l'admin
        remember_me: true                 # Activer la fonctionnalité "Se souvenir de moi"
        session_lifetime: 3600            # Durée de vie de la session en secondes
        password_hasher: 'auto'           # Configuration du hashage de mot de passe
        login_route: 'dashify_login'      # Route de connexion
        login_redirect_route: 'dashify_dashboard'  # Route après connexion
        logout_route: 'dashify_logout'    # Route de déconnexion
        logout_redirect_route: 'dashify_login'     # Route après déconnexion
        enable_login: true                # Activer/désactiver la page de connexion
```

## Utilisation

Une fois installé, vous pouvez accéder à l'interface d'administration à l'URL `/admin`.

### Menu Structure

The menu configuration supports:
- Multiple groups with custom labels and icons
- Optional positioning for groups
- Separate sections for users and content within each group
- Each menu item requires a label and route
- Icons are optional for all items 