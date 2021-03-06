CiriciJWTClientBundle
=====================

[![Build status][build svg]][build status]
[![Code coverage][coverage svg]][coverage]
[![License][license svg]][license]
[![Latest stable version][releases svg]][releases]
[![Total downloads][downloads svg]][downloads]
[![Code climate][climate svg]][climate]

This Bundle is used to login against a JWT server or to check the validity of a JWT Token

It has been based on [these instructions][instructions].

WARNING! This bundle is Work In Progress and is not ready for production yet

## Installation

```bash
composer require ciricihq/jwt-client-bundle:dev-master
```

Then add to `AppKernel.php`

```php
        $bundles = [
            ...
            new Cirici\JWTClientBundle\CiriciJWTClientBundle(),
            ...
        ];
```

## Configuration

If you are planning to use the bundle as a Authentication service against a JWT server,
you should load the external token authenticator adding this to your `config.yml`

```yaml
cirici_jwt_client:
    use_external_jwt_api: true
    external_api: "@eight_points_guzzle.client.api_jwt"
    jwt_token_path: /jwt/token # Endpoint where the token POST request will be done
```

And you must define the api using Guzzle configuration

```yaml
guzzle:
    clients:
        api_jwt:
            base_url: %api_jwt_base_url%
```

## Configure security for login form against external JWT server

In order to make this bundle work you should define your `security.yml` like this

```yaml
# To get started with security, check out the documentation:
security:
    providers:
        token:
            id: project.token.user_provider

    firewalls:
        # disables authentication for assets and the profiler, adapt it according to your needs
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false

        main:
            pattern: ^/
            provider: token
            anonymous: true
            simple_form:
                authenticator: project.token.external_authenticator
                check_path: login_check
                login_path: login
                # user_referer: true
                failure_path: login
            logout:
                path: /logout
                target: login
            remember_me:
                secret: '%secret%'
                lifetime: 86400
                path: /

    access_control:
        - { path: ^/login, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/registration, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/, role: ROLE_ADMIN }
```

In `routes.yml` you has to add a login path as those lines for the login fails redirect and add
the bundle routes import as well:

```yaml
jwt_client:
    resource: '@CiriciJWTClientBundle/Resources/config/routing.yml'
    prefix: /

login:
    path: /login
```

## Setting up custom User class for incoming requests

If you want to map the incoming token calls with a custom User class instead of ApiUser you should implement `Cirici\JWTClientBundle\Security\ApiUserInterface` in your custom User class.
Then configure your custom User class in `config.yml`:

```yaml
cirici_jwt_client:
    api_user_class: '\AppBundle\Entity\User'
```

## Configure to validate incoming Authentication bearer

In your `security.yml` firewall you has to add the next lines:

```yaml
security:
    providers:
        token:
            id: project.token.user_provider

    firewalls:
        api:
            pattern:   ^/api/user
            stateless: true
            guard:
                provider: token
                authenticators:
                    - project.token.authenticator
```

## Extending login template

If you want to modify the default login template you should create the next folders

```bash
mkdir -P app/Resources/CiriciJWTClientBundle/views/Security
```

And then copy the file `login.html.twig` from the bundle to the folder created above.

Now your app will load the login template just copied and you can modify it without altering the bundle one. :)

[build status]: https://travis-ci.org/ciricihq/CiriciJWTClientBundle
[coverage]: https://codecov.io/gh/ciricihq/CiriciJWTClientBundle
[license]: https://github.com/ciricihq/CiriciJWTClientBundle/blob/master/LICENSE.md
[releases]: https://github.com/ciricihq/CiriciJWTClientBundle/releases
[downloads]: https://packagist.org/packages/ciricihq/CiriciJWTClientBundle
[climate]: https://codeclimate.com/github/ciricihq/CiriciJWTClientBundle

[build svg]: https://img.shields.io/travis/ciricihq/CiriciJWTClientBundle/master.svg?style=flat-square
[coverage svg]: https://img.shields.io/codecov/c/github/ciricihq/CiriciJWTClientBundle/master.svg?style=flat-square
[license svg]: https://img.shields.io/github/license/ciricihq/CiriciJWTClientBundle.svg?style=flat-square
[releases svg]: https://img.shields.io/github/release/ciricihq/CiriciJWTClientBundle.svg?style=flat-square
[downloads svg]: https://img.shields.io/packagist/dt/ciricihq/CiriciJWTClientBundle.svg?style=flat-square
[climate svg]: https://img.shields.io/codeclimate/github/ciricihq/CiriciJWTClientBundle.svg?style=flat-square

[instructions]: http://ypereirareis.github.io/blog/2016/03/16/symfony-lexikjwtauthenticationbundle-client-user-authenticator-provider/
