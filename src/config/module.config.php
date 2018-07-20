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
            \OAuth\Middleware\OAuth2::class
                => \OAuth\Middleware\OAuth2Factory::class,
            \OAuth\Middleware\Authentication::class
                => \OAuth\Middleware\AuthenticationFactory::class,
            \OAuth\EventListener\OAuthEventSubscriber::class
                => \OAuth\EventListener\OAuthEventSubscriberFactory::class,


            \OAuth\Config\Config::class
                => \OAuth\Config\ConfigFactory::class,
            \League\OAuth2\Server\AuthorizationServer::class
                => \OAuth\AuthorizationServerFactory::class,
            \League\OAuth2\Server\ResourceServer::class
                => \OAuth\ResourceServerFactory::class,
            \League\OAuth2\Server\AuthorizationValidators\BearerTokenValidator::class
                => \OAuth\AuthorizationValidators\BearerTokenValidatorFactory::class,

            /* Commands */
            \OAuth\Command\KeyGenerator::class
                => \OAuth\Command\KeyGeneratorFactory::class,

            \OAuth\Command\Scope\Create::class
                => \OAuth\Command\Scope\CreateFactory::class,
            \OAuth\Command\Scope\Delete::class
                => \OAuth\Command\Scope\DeleteFactory::class,

            \OAuth\Command\Client\Create::class
                => \OAuth\Command\Client\CreateFactory::class,
            \OAuth\Command\Client\Modify::class
                => \OAuth\Command\Client\ModifyFactory::class,
            \OAuth\Command\Client\Grants::class
                => \OAuth\Command\Client\GrantsFactory::class,
            \OAuth\Command\Client\Scopes::class
                => \OAuth\Command\Client\ScopesFactory::class,
            \OAuth\Command\Client\Secret::class
                => \OAuth\Command\Client\SecretFactory::class,
            \OAuth\Command\Client\Delete::class
                => \OAuth\Command\Client\DeleteFactory::class,

            /* Grants */
            \League\OAuth2\Server\Grant\AuthCodeGrant::class
                => \OAuth\Grant\AuthorizationCodeGrantFactory::class,
            \League\OAuth2\Server\Grant\ClientCredentialsGrant::class
                => \OAuth\Grant\ClientCredentialsGrantFactory::class,
            \League\OAuth2\Server\Grant\ImplicitGrant::class
                => \OAuth\Grant\ImplicitGrantFactory::class,
            \League\OAuth2\Server\Grant\PasswordGrant::class
                => \OAuth\Grant\PasswordGrantFactory::class,
            \League\OAuth2\Server\Grant\RefreshTokenGrant::class
                => \OAuth\Grant\RefreshTokenGrantFactory::class,
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
                    \OAuth\EventListener\OAuthEventSubscriber::class
                        => \OAuth\EventListener\OAuthEventSubscriber::class,
                ],
            ],
        ],
    ],

    'console' => [
        'commands' => [
            \OAuth\Command\KeyGenerator::class => \OAuth\Command\KeyGenerator::class,

            \OAuth\Command\Scope\Create::class => \OAuth\Command\Scope\Create::class,
            \OAuth\Command\Scope\Delete::class => \OAuth\Command\Scope\Delete::class,

            \OAuth\Command\Client\Create::class => \OAuth\Command\Client\Create::class,
            \OAuth\Command\Client\Modify::class => \OAuth\Command\Client\Modify::class,
            \OAuth\Command\Client\Grants::class => \OAuth\Command\Client\Grants::class,
            \OAuth\Command\Client\Scopes::class => \OAuth\Command\Client\Scopes::class,
            \OAuth\Command\Client\Secret::class => \OAuth\Command\Client\Secret::class,
            \OAuth\Command\Client\Delete::class => \OAuth\Command\Client\Delete::class,
        ],
    ],
];
