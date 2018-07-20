<?php

declare(strict_types=1);

namespace WShafer\OAuth\Middleware;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use WShafer\OAuth\Config\Config;
use WShafer\OAuth\Exception\OAuthHttpProblem;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Session\SessionInterface;
use Zend\Expressive\Session\SessionMiddleware;

class OAuth2 implements RequestHandlerInterface
{
    protected $server;

    protected $config;

    protected $router;

    protected $responseFactory;

    public function __construct(
        Config $config,
        AuthorizationServer $server,
        RouterInterface $router,
        callable $responseFactory
    ) {
        $this->config = $config;
        $this->server = $server;
        $this->router = $router;
        $this->responseFactory = function () use ($responseFactory) : ResponseInterface {
            return $responseFactory();
        };
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $method = $request->getMethod();
        switch (strtoupper($method)) {
            case 'GET':
                return $this->authorizationRequest($request);
            case 'POST':
                return $this->accessTokenRequest($request);
        }
        return ($this->responseFactory)()->withStatus(501); // Method not implemented
    }

    /**
     * Authorize the request and return an authorization code
     * Used for authorization code grant and implicit grant
     *
     * @see https://oauth2.thephpleague.com/authorization-server/auth-code-grant/
     * @see https://oauth2.thephpleague.com/authorization-server/implicit-grant/
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    protected function authorizationRequest(ServerRequestInterface $request) : ResponseInterface
    {
        // Create a new response for the request
        /** @var ResponseInterface $response */
        $response = ($this->responseFactory)();

        try {
            // Validate the HTTP request and return an AuthorizationRequest object.
            $authRequest = $this->server->validateAuthorizationRequest($request);

            // The auth request object can be serialized and saved into a user's session.
            // You will probably want to redirect the user at this point to a login endpoint.

            /** @var SessionInterface $session */
            $session = $request->getAttribute(SessionMiddleware::SESSION_ATTRIBUTE);
            $session->set('oauth_request', serialize($authRequest));

            return $response->withStatus('302')
                ->withHeader(
                    'Location',
                    $this->router->generateUri($this->config->getAuthenticationRouteName())
                );

        } catch (OAuthServerException $exception) {
            throw OAuthHttpProblem::create($exception);
        }
    }

    /**
     * Request an access token
     * Used for client credential grant, password grant, and refresh token grant
     *
     * @see https://oauth2.thephpleague.com/authorization-server/client-credentials-grant/
     * @see https://oauth2.thephpleague.com/authorization-server/resource-owner-password-credentials-grant/
     * @see https://oauth2.thephpleague.com/authorization-server/refresh-token-grant/
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    protected function accessTokenRequest(ServerRequestInterface $request) : ResponseInterface
    {
        // Create a new response for the request
        $response = ($this->responseFactory)();

        try {
            return $this->server->respondToAccessTokenRequest($request, $response);
        } catch (OAuthServerException $exception) {
            throw OAuthHttpProblem::create($exception);
        }
    }
}
