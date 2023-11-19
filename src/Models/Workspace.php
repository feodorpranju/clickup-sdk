<?php


namespace Feodorpranju\ClickUp\SDK\Models;


use Feodorpranju\ApiOrm\Contracts\QueryBuilderInterface;
use Feodorpranju\ApiOrm\Support\ModelCollection;
use Feodorpranju\ClickUp\SDK\Models\Fields\FieldSettings;
use Feodorpranju\ClickUp\SDK\Traits\HasAfterSelectFilter;
use Feodorpranju\ClickUp\SDK\Traits\HasChildren;
use Illuminate\Support\Collection;
use Feodorpranju\ApiOrm\Enumerations\FieldType;

class Workspace extends AbstractModel
{
    use HasAfterSelectFilter, HasChildren;

    public const ENTITY_NAME = 'workspace';

    public function __construct(array|Collection $attributes = [])
    {
        $attributes = collect($attributes);

        $this->collectModelField($attributes, 'members', User::class);

        parent::__construct($attributes);
    }

    public function fields(): Collection
    {
        return collect([
            'id' => new FieldSettings('id', FieldType::Int),
            'name' => new FieldSettings('name', FieldType::String),
            'color' => new FieldSettings('color', FieldType::String),
            'avatar' => new FieldSettings('avatar', FieldType::Url),
            'statuses' => new FieldSettings('statuses', FieldType::Cast, true),
            'features' => new FieldSettings('features', FieldType::Cast, true),
            'private' => new FieldSettings('private', FieldType::Bool),
            'admin_can_manage' => new FieldSettings('admin_can_manage', FieldType::Bool),
            'multiple_assignees' => new FieldSettings('multiple_assignees', FieldType::Bool),
            'archived' => new FieldSettings('archived', FieldType::Bool),
        ]);
    }

    public function get(int|string $id): static
    {
        return static::where('id', $id)->firstOrFail();
    }

    /**
     * Gets plan details
     *
     * @return array
     */
    public function getPlan(): array
    {
        return static::cmd("team/{$this->id()}/plan", "get")->call()->getResult()->toArray();
    }

    /**
     * Gets plan details
     *
     * @return array
     */
    public function getSeats(): array
    {
        return static::cmd("team/{$this->id()}/seats", "get")->call()->getResult()->toArray();
    }

    /**
     * @return ModelCollection
     */
    public function all(): ModelCollection
    {
        return static::collect(
            static::cmd('team', 'get')
                ->call()
                ->getResult()
                ->get('teams')
        );
    }

    public function spaces(): QueryBuilderInterface
    {
        return $this->selectChildren(Space::class);
    }

    public function folders(): QueryBuilderInterface
    {
        return $this->selectChildren(Folder::class);
    }

    public function lists(): QueryBuilderInterface
    {
        return $this->selectChildren(Listing::class);
    }

    public function tasks(): QueryBuilderInterface
    {
        return $this->selectChildren(Task::class);
    }

    public function timeEntries(): QueryBuilderInterface
    {
        return $this->selectChildren(TimeEntry::class);
    }
}