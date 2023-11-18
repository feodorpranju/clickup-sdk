<?php


namespace Feodorpranju\ClickUp\SDK\Core;


use Feodorpranju\ApiOrm\Http\AbstractResponse;
use Illuminate\Support\Collection;

class Response extends AbstractResponse
{

    /**
     * @inheritDoc
     */
    public function getResult(): Collection
    {
        return collect($this->getHttpResponse()->json());
    }
}