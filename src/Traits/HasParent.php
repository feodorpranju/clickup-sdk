<?php


namespace Feodorpranju\ClickUp\SDK\Traits;


use Feodorpranju\ApiOrm\Support\ModelCollection;
use Feodorpranju\ClickUp\SDK\Models\AbstractModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HasParent
{
    use HasGetConditions;

    private ?array $parentKeys = null;

    public function getByParentId(int $parentId, Collection $conditions): ModelCollection
    {
        $parentClass = static::PARENT;

        return static::collect(
            $this->cmd(
                Str::replace('?', $parentId, static::GET_BY_PARENT_PATTERN),
                'get',
                $this->generateSelectData($conditions)
            )
                ->call()
                ->getResult()
                ->get(
                    defined(static::class.'::FIND_RESULT_KEY')
                        ? static::FIND_RESULT_KEY
                        : Str::plural(static::ENTITY_NAME)
                )
        )->map(
            fn($entity) => $entity->put([
                $parentClass::ENTITY_NAME.'_id' => $parentId
            ])
        );
    }

    public function getByParentIds(array|Collection $parentIds, Collection $conditions): ModelCollection
    {
        $entities = collect();

        foreach ($parentIds as $parentId) {
            $entities = $entities->merge(
                $this->getByParentId($parentId, $conditions)
            );
        }

        return ModelCollection::make($entities);
    }

    /**
     * Selects and filters by conditions checking parents and their parents recursively
     *
     * @param Collection $conditions
     * @return ModelCollection
     */
    public function getByParentsRecursive(Collection $conditions): ModelCollection
    {
        $parentClass = static::PARENT;

        $parentCondition = $conditions->filter(
            fn($cond) => $cond[0] === $parentClass::ENTITY_NAME.'_id' && in_array($cond[1], ['', '=', '=='])
        )->first();

        if ($parentCondition) {
            $entities = $this->getByParentIds((array)$parentCondition[2], $conditions);
        } elseif (method_exists($parentClass, 'getByParentsRecursive')) {
            $entities = $this->getByParents(
                $parentClass::withClient($this->getClient())
                    ->getByParentsRecursive(
                        $this->filterParentConditions($conditions, false)
                    ),
                collect()
            );
        } else {
            $entities = $this->all();
        }

        return $entities->filterByConditions(
            $this->filterParentConditions(
                $this->filterAfterSelectConditions($conditions)
            )
        );
    }

    /**
     * @param Collection $conditions
     * @param bool $clearParents leaves parent id conditions on false
     * @return Collection
     */
    private function filterParentConditions(Collection $conditions, bool $clearParents = true): Collection
    {
        return $conditions->filter(
            fn($condition) => $clearParents
                ? !$this->isParentCondition($condition)
                : $this->isParentCondition($condition)
        );
    }

    private function isParentCondition(array $condition): bool
    {
        return in_array($condition[0], $this->getParentIdKeys())
            && in_array($condition[1], ['', '=', '==']);
    }

    /**
     * @inheritDoc
     */
    public function all(): ModelCollection
    {
        $parentClass = static::PARENT;

        return $this->getByParents(
            $parentClass::withClient($this->getClient())->all(),
        );
    }

    /**
     * @inheritDoc
     */
    public function find(array|Collection $conditions = [], string $orderBy = null, string $orderDirection = null, array $select = [], int $offset = 0, int $limit = 50): ModelCollection
    {
        $conditions = collect($conditions);

        return $this->getByParentsRecursive($conditions);
    }


    /**
     * Gets entities by it's parent
     * Checks if parent contains children not to send requests
     *
     * @param AbstractModel $parent
     * @param Collection $conditions
     * @return ModelCollection
     */
    public function getByParent(AbstractModel $parent, Collection $conditions): ModelCollection
    {
        if (static::EXISTS_IN_PARENT) {
            $key = Str::plural(static::ENTITY_NAME);

            return ModelCollection::make($parent->$key);
        }

        return $this->getByParentId($parent->id(), $conditions);
    }

    /**
     * Gets entities by it's parents
     * Checks if parent contains children not to send requests
     *
     * @param Collection $parents
     * @param Collection $conditions
     * @return ModelCollection
     */
    public function getByParents(Collection $parents, Collection $conditions): ModelCollection
    {
        $entities = new ModelCollection();

        foreach ($parents as $parent) {
            $entities = $entities->merge($this->getByParent($parent, $conditions));
        }

        return $entities;
    }

    public function getParentIdKeys(): array
    {
        if ($this->parentKeys) {
            return $this->parentKeys;
        }

        $parentClass = static::PARENT;
        $stack = [$parentClass::ENTITY_NAME.'_id'];

        if (method_exists($parentClass, 'getParentIdKeys')) {
            $stack = array_merge(
                $stack,
                $parentClass::make()->getParentIdKeys()
            );
        }

        return $this->parentKeys = $stack;
    }
}