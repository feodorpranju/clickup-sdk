<?php


namespace Feodorpranju\ClickUp\SDK\Traits;


use Feodorpranju\ApiOrm\Contracts\QueryBuilderInterface;

trait HasChildren
{
    /**
     * Selects children
     *
     * @param string $childClass
     * @param string|null $parentClass Current if null
     * @param array|null $parentIds
     * @return QueryBuilderInterface
     */
    public function selectChildren(string $childClass, ?string $parentClass = null, ?array $parentIds = null): QueryBuilderInterface
    {
        $parentClass ??= $childClass::PARENT ?? static::class;

        if (!$parentIds) {
            $parentIds = $parentClass === static::class
                ? $this->id()
                : $parentClass::where(static::ENTITY_NAME.'_id', $this->id())
                    ->withClient(static::getClient())
                    ->all()
                    ->pluck('id');
        }

        return $childClass::where(
            $parentClass::ENTITY_NAME.'_id',
            $parentIds
        )->withClient(static::getClient());
    }
}