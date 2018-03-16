# Kolyya OAuth Bundle

Installation
============

1. Установить и настроить `friendsofsymfony/user-bundle`

2. Установить и настроить `hwi/oauth-bundle`

Applications that use Symfony Flex
----------------------------------

Open a command console, enter your project directory and execute:

```console
$ composer require <package-name>
```

Applications that don't use Symfony Flex
----------------------------------------

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require <package-name>
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...
            new <vendor>\<bundle-name>\<bundle-long-name>(),
        );

        // ...
    }

    // ...
}
```

## Integration

В `config/services.yaml` сделать сервис публичным:

```yaml
services:
    # ...
    kolyya.oauth.user.provider:
        class: Kolyya\OAuthBundle\Security\Core\User\OAuthUserProvider
        arguments: ['','','','%env(MAILER_USER)%']
        public: true
```

и установить его в `config/packages/security.yaml`

```yaml
security:
    # ...
    firewalls:
        # ...
        main:
            # ...
            oauth:
                # ...
                oauth_user_provider:
                    service: kolyya.oauth.user.provider
        # ...
```

и в конфиг для `hwi_oauth`:

```yaml
hwi_oauth:
    # ...
    connect:
        account_connector: kolyya.oauth.user.provider
    # ...
```

## Configuration

### Настройка User Entity

Унаследовать User от Kolyya\OAuthBundle\Entity\OAuthUser:

```php
// src/Entity/User.php

// ...
use Kolyya\OAuthBundle\Entity\OAuthUser;

// ...
class User extends OAuthUser
{
// ...

```

Добавить свойства для хранения данных из соц сетей:

```php
// src/Entity/User.php

// ...
class User extends OAuthUser
{
    // ...
    /**
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $vkontakteData;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    protected $vkontakteId;
    
    /**
     * @return mixed
     */
    public function getVkontakteData()
    {
        return $this->vkontakteData;
    }

    /**
     * @param mixed $vkontakteData
     */
    public function setVkontakteData($vkontakteData)
    {
        $this->vkontakteData = $vkontakteData;
    }
    
    /**
     * @return mixed
     */
    public function getVkontakteId()
    {
        return $this->vkontakteId;
    }

    /**
     * @param mixed $vkontakteId
     */
    public function setVkontakteId($vkontakteId)
    {
        $this->vkontakteId = $vkontakteId;
    }
    
    // ...
}

```

### Добавить кнопки на страницу входа

```twig
{# templates/bundles/FOSUserBundle/Security/login.html.twig #}

{# ... #}
{{ kolyya_oauth_buttons() }}
{# ... #}

```

### Добавить маршруты

```yaml
# config/routes.yaml
# ...
kolyya_oauth:
    resource: "@KolyyaOAuthBundle/Resources/config/routing.yml"
    prefix:   /
```