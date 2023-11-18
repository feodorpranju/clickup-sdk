<?php


namespace Feodorpranju\ClickUp\SDK\Models;


use Feodorpranju\ApiOrm\Contracts\Http\ApiClientInterface;
use Feodorpranju\ApiOrm\Contracts\ModelSearchableInterface;
use Feodorpranju\ApiOrm\Contracts\QueryBuilderInterface;
use Feodorpranju\ApiOrm\Models\DefaultQueryBuilder;
use Feodorpranju\ClickUp\SDK\Core\Cmd;
use Illuminate\Support\Collection;
use Feodorpranju\ApiOrm\Enumerations\FieldType;

/**
 * Class AbstractModel
 * @package Feodorpranju\ClickUp\SDK\Models
 * @method static withClient(ApiClientInterface $client)
 */
abstract class AbstractModel extends \Feodorpranju\ApiOrm\Models\AbstractModel implements ModelSearchableInterface
{
    protected static ApiClientInterface $defaultClient;
    public const ENTITY_NAME = null;

    public function get(int|string $id): ?static
    {
        static::put(
            static::cmd(static::ENTITY_NAME."/$id", 'get')
                ->call()
                ->getResult()
        );
        $this->updatedFields = [];

        return $this;
    }

    public function __construct(array|Collection $attributes = [])
    {
        parent::__construct(
            self::correctDateFields(collect($attributes))
        );
    }

    protected function collectModelField(Collection $attributes, string $key, string $modelClass)
    {
        if ($attributes->has($key)) {
            $attributes->put($key, collect($attributes->get($key))
                ->map(
                    fn($model) => (
                    is_a($model, $modelClass, true)
                        ? $model
                        : $modelClass::make($model, $this->getClient())
                    )->put([static::ENTITY_NAME.'_id' => $attributes->get('id')])
                )
            );
        }
    }

    /**
     * Returns model id
     *
     * @return int|string|null
     */
    public function id(): int|string|null
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public static function where(array|string $field, mixed $operand = null, mixed $value = null): QueryBuilderInterface
    {
        return static::select()->where($field, $operand, $value);
    }

    /**
     * Corrects numeric date, time and datetime fields
     * Numeric dates are received in milliseconds but we need seconds
     * so we are converting them to seconds
     *
     * @param Collection $attributes
     * @return Collection
     */
    protected function correctDateFields(Collection $attributes): Collection
    {
        foreach (static::fields() as $id => $settings) {
            if (
                $attributes->has($id)
                && is_numeric($attributes->get($id))
                && in_array($settings->type()->value, ['date', 'time', 'datetime'])
            ) {
                $attributes->put($id, $attributes->get($id)/1000);
            }
        }

        return $attributes;
    }

    public static function setDefaultClient(ApiClientInterface $client): void
    {
        static::$defaultClient = $client;
    }

    public function getClient(): ?ApiClientInterface
    {
        return $this->client ??= static::$defaultClient ?? null;
    }

    protected function cmd(
        string $action,
        string $method,
        array $params = [],
        string $version = 'v2',
    ): Cmd
    {
        return Cmd::make($action, $method, $params, $version, $this->getClient());
    }

    /**
     * @inheritDoc
     */
    public static function select(array $fields = null): QueryBuilderInterface
    {
        return (new DefaultQueryBuilder(static::class))->select($fields ?? []);
    }

    public static function __callStatic(string $name, array $arguments)
    {
        $parent = parent::__callStatic($name, $arguments);

        if ($parent) {
            return $parent;
        }

        return match ($name) {
            'get' => static::make()->get(...$arguments)
        };
    }

    /**
     * @inheritDoc
     */
    public function count(array|Collection $conditions): int
    {
        return 0;
    }
}