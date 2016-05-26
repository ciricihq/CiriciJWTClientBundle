This Bundle is used to login against a JWT server

Is based on this instructions: http://ypereirareis.github.io/blog/2016/03/16/symfony-lexikjwtauthenticationbundle-client-user-authenticator-provider/

## Configuration

The first step is to configure jms/di-extra-bundle to load the Dependency Injection annotations so you have to configure the paths to look for.

```
jms_di_extra:
    locations:
        all_bundles: false
        bundles: [AppBundle]
        directories: ["%kernel.root_dir%/../src", "%kernel.root_dir%/../vendor/cirici/jwt-client-bundle"]

```

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
                authenticator: project.token.authenticator
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

## Extending login template

If you want to modify the default login template you should create the next folders

```bash
mkdir -P app/Resources/CiriciJWTClientBundle/views/Security
```

And then copy the file `login.html.twig` from the bundle to the folder created above.

Now your app will load the login template just copied and you can modify it without altering the bundle one. :)
