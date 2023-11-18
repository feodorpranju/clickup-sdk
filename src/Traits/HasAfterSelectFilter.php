<?php


namespace Feodorpranju\ClickUp\SDK\Traits;


use Feodorpranju\ApiOrm\Support\ModelCollection;
use Illuminate\Support\Collection;

trait HasAfterSelectFilter
{
    /**
     * @inheritdoc
     */
    public function find(array|Collection $conditions = [], string $orderBy = null, string $orderDirection = null, array $select = [], int $offset = 0, int $limit = 50): ModelCollection
    {
        return static::withClient($this->getClient())->all()->filterByConditions($conditions);
    }
}