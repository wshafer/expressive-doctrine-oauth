<?php

return [
    'oauth2' => [
        'privateKeyPath'             => __DIR__ . '/../../../../data/private.key',
        'publicKeyPath'              => __DIR__ . '/../../../../data/public.key',
        'encryptionKeyPath'          => __DIR__ . '/../../../../data/encryption.key',
        'accessTokenExpireInterval'  => 'P1D',   // 1 day in DateInterval format
        'refreshTokenExpireInterval' => 'P1M',   // 1 month in DateInterval format
        'authCodeExpireInterval'     => 'PT10M', // 10 minutes in DateInterval format
        'authenticationRouteName'     => 'auth',
        // Password Hash Params
        'passwordHash' => [
            'algorithm' => PASSWORD_DEFAULT,
            'options' => []
        ],

        'grants' => [
            // Grants list should be [identifierName] => serviceName
            'authorization_code' => 'authorization_code',
            'client_credentials' => 'client_credentials',
            'implicit'           => 'implicit',
            'password'           => 'password',
            'refresh_token'      => 'refresh_token',
        ]
    ],

    'dependencies' => [
        'aliases' => [
            'Oauth\Doctrine\EntityManager' => 'doctrine.entity_manager.orm_default',
            'authorization_code' => \League\OAuth2\Server\Grant\AuthCodeGrant::class,
            'client_credentials' => \League\OAuth2\Server\Grant\ClientCredentialsGrant::class,
            'implicit'           => \League\OAuth2\Server\Grant\ImplicitGrant::class,
            'password'           => \League\OAuth2\Server\Grant\PasswordGrant::class,
            'refresh_token'      => \League\OAuth2\Server\Grant\RefreshTokenGrant::class,
        ],
        'factories' => [
            \WShafer\OAuth\Middleware\OAuth2::class
                => \WShafer\OAuth\Middleware\OAuth2Factory::class,
            \WShafer\OAuth\Middleware\Authentication::class
                => \WShafer\OAuth\Middleware\AuthenticationFactory::class,
            \WShafer\OAuth\EventListener\OAuthEventSubscriber::class
                => \WShafer\OAuth\EventListener\OAuthEventSubscriberFactory::class,


            \WShafer\OAuth\Config\Config::class
                => \WShafer\OAuth\Config\ConfigFactory::class,
            \League\OAuth2\Server\AuthorizationServer::class
                => \WShafer\OAuth\AuthorizationServerFactory::class,
            \League\OAuth2\Server\ResourceServer::class
                => \WShafer\OAuth\ResourceServerFactory::class,
            \League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator::class
                => \WShafer\OAuth\AuthorizationValidators\BearerTokenValidatorFactory::class,

            /* Commands */
            \WShafer\OAuth\Command\KeyGenerator::class
                => \WShafer\OAuth\Command\KeyGeneratorFactory::class,

            \WShafer\OAuth\Command\Scope\Create::class
                => \WShafer\OAuth\Command\Scope\CreateFactory::class,
            \WShafer\OAuth\Command\Scope\Delete::class
                => \WShafer\OAuth\Command\Scope\DeleteFactory::class,

            \WShafer\OAuth\Command\Client\Create::class
                => \WShafer\OAuth\Command\Client\CreateFactory::class,
            \WShafer\OAuth\Command\Client\Modify::class
                => \WShafer\OAuth\Command\Client\ModifyFactory::class,
            \WShafer\OAuth\Command\Client\Grants::class
                => \WShafer\OAuth\Command\Client\GrantsFactory::class,
            \WShafer\OAuth\Command\Client\Scopes::class
                => \WShafer\OAuth\Command\Client\ScopesFactory::class,
            \WShafer\OAuth\Command\Client\Secret::class
                => \WShafer\OAuth\Command\Client\SecretFactory::class,
            \WShafer\OAuth\Command\Client\Delete::class
                => \WShafer\OAuth\Command\Client\DeleteFactory::class,

            /* Grants */
            \League\OAuth2\Server\Grant\AuthCodeGrant::class
                => \WShafer\OAuth\Grant\AuthorizationCodeGrantFactory::class,
            \League\OAuth2\Server\Grant\ClientCredentialsGrant::class
                => \WShafer\OAuth\Grant\ClientCredentialsGrantFactory::class,
            \League\OAuth2\Server\Grant\ImplicitGrant::class
                => \WShafer\OAuth\Grant\ImplicitGrantFactory::class,
            \League\OAuth2\Server\Grant\PasswordGrant::class
                => \WShafer\OAuth\Grant\PasswordGrantFactory::class,
            \League\OAuth2\Server\Grant\RefreshTokenGrant::class
                => \WShafer\OAuth\Grant\RefreshTokenGrantFactory::class,
        ],
    ],

    'doctrine' => [
        'driver' => [
            'orm_default' => [
                'paths'     => [__DIR__ . '/../src/Entity'],
            ],
        ],

        'event_manager' => [
            'orm_default' => [
                'subscribers' => [
                    \WShafer\OAuth\EventListener\OAuthEventSubscriber::class
                        => \WShafer\OAuth\EventListener\OAuthEventSubscriber::class,
                ],
            ],
        ],
    ],

    'console' => [
        'commands' => [
            \WShafer\OAuth\Command\KeyGenerator::class => \WShafer\OAuth\Command\KeyGenerator::class,
            \WShafer\OAuth\Command\Scope\Create::class => \WShafer\OAuth\Command\Scope\Create::class,
            \WShafer\OAuth\Command\Scope\Delete::class => \WShafer\OAuth\Command\Scope\Delete::class,
            \WShafer\OAuth\Command\Client\Create::class => \WShafer\OAuth\Command\Client\Create::class,
            \WShafer\OAuth\Command\Client\Modify::class => \WShafer\OAuth\Command\Client\Modify::class,
            \WShafer\OAuth\Command\Client\Grants::class => \WShafer\OAuth\Command\Client\Grants::class,
            \WShafer\OAuth\Command\Client\Scopes::class => \WShafer\OAuth\Command\Client\Scopes::class,
            \WShafer\OAuth\Command\Client\Secret::class => \WShafer\OAuth\Command\Client\Secret::class,
            \WShafer\OAuth\Command\Client\Delete::class => \WShafer\OAuth\Command\Client\Delete::class,
        ],
    ],
];
