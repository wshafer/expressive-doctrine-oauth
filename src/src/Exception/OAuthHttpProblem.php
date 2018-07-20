<?php

namespace WShafer\OAuth\Exception;

use League\OAuth2\Server\Exception\OAuthServerException;
use Zend\ProblemDetails\Exception\CommonProblemDetailsExceptionTrait;
use Zend\ProblemDetails\Exception\ProblemDetailsExceptionInterface;

class OAuthHttpProblem extends \RuntimeException implements ProblemDetailsExceptionInterface
{
    use CommonProblemDetailsExceptionTrait;

    public static function create(OAuthServerException $exception)
    {
        $e = new self($exception->getMessage());
        $e->status = $exception->getHttpStatusCode();
        $e->detail = $exception->getMessage();
        $e->type = 'https://httpstatuses.com/'.$exception->getHttpStatusCode();
        $e->title = $exception->getErrorType();

        throw $e;
    }
}
