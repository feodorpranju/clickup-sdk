<?php


namespace Feodorpranju\ClickUp\SDK\Core;


use Feodorpranju\ApiOrm\Http\AbstractApiClient;
use Illuminate\Support\Facades\Http;

class ApiClient extends AbstractApiClient
{
    /**
     * SendsApiRequest
     *
     * @param string $action
     * @param array $params
     * @param string $method
     * @return Response
     */
    public function call(string $action, array $params = [], string $method = 'POST'): Response
    {
        $url = $this->getCredentials()->getDomainUrl().'/'.$action;
        $method = strtolower($method);
        return Response::make(
            Http::withHeader('Authorization', $this->getCredentials()->getAuthToken())
                ->$method($url, $params)
        );
    }
}