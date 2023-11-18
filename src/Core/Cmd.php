<?php


namespace Feodorpranju\ClickUp\SDK\Core;


use Feodorpranju\ApiOrm\Contracts\Http\ApiClientInterface;
use Feodorpranju\ApiOrm\Contracts\Http\CommandInterface;
use Feodorpranju\ApiOrm\Contracts\Http\ResponseInterface;

class Cmd implements CommandInterface
{
    public function __construct(
        protected string $action,
        protected string $method,
        protected array $params = [],
        protected string $version = 'v2',
        protected ?ApiClientInterface $client = null,
    ) { }

    public static function make(
        string $action,
        string $method,
        array $params = [],
        string $version = 'v2',
        ?ApiClientInterface $client = null,

    ): static
    {
        return new static($action, $method, $params, $version, $client);
    }

    /**
     * @inheritDoc
     */
    public function call(?ApiClientInterface $client = null): Response
    {
        if ($client) {
            $this->setClient($client);
        }

        return $this->getClient()->call(
            $this->version.'/'.$this->action,
            $this->params,
            $this->method,
        );
    }

    public function setClient(ApiClientInterface $client): void
    {
        $this->client = $client;
    }

    public function withClient(ApiClientInterface $client): static
    {
        $this->setClient($client);
        return $this;
    }

    public function getClient(): ?ApiClientInterface
    {
        return $this->client;
    }
}