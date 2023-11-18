<?php


namespace Feodorpranju\ClickUp\SDK\Core\Credentials;


use Feodorpranju\ApiOrm\Contracts\Http\CredentialsInterface;

/**
 * Class AuthToken
 * @package Feodorpranju\ClickUp\SDK\Core\Credentials
 * @method static static make(string $authToken)
 */
class AuthToken implements CredentialsInterface
{
    protected string $url = 'https://api.clickup.com/api';

    public function __construct(protected string $authToken)
    {
    }

    public function getDomainUrl(): string
    {
        return trim($this->url, '/');
    }

    public function setDomainUrl(string $domain): void
    {
        $this->url = $domain;
    }

    public function getAuthMethod(): string
    {
        return 'bearer';
    }

    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    public static function __callStatic(string $name, array $arguments)
    {
        return match ($name) {
            'make' => new static(...$arguments),
            'default' => null
        };
    }
}