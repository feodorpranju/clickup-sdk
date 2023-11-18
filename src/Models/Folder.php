<?php


namespace Feodorpranju\ClickUp\SDK\Models;


use Feodorpranju\ApiOrm\Contracts\QueryBuilderInterface;
use Feodorpranju\ClickUp\SDK\Models\Fields\FieldSettings;
use Feodorpranju\ClickUp\SDK\Traits\HasChildren;
use Feodorpranju\ClickUp\SDK\Traits\HasParent;
use Illuminate\Support\Collection;
use Feodorpranju\ApiOrm\Enumerations\FieldType;

class Folder extends AbstractModel
{
    use HasParent, HasChildren;

    public const ENTITY_NAME = 'folder';

    /**
     * For HasParent trait
     */
    public const PARENT = Space::class;
    public const GET_BY_PARENT_PATTERN = 'space/?/folder';
    public const EXISTS_IN_PARENT = false;
    public const GET_FILTERS = ['archived'];

    public function __construct(array|Collection $attributes = [])
    {
        $attributes = collect($attributes);

        $this->collectModelField($attributes, 'lists', Listing::class);

        parent::__construct($attributes);
    }

    public function fields(): Collection
    {
        return collect([
            'id' => new FieldSettings('id', FieldType::Int),
            'space_id' => new FieldSettings('space_id', FieldType::Int),
            'task_count' => new FieldSettings('task_count', FieldType::Int),
            'orderindex' => new FieldSettings('orderindex', FieldType::Int),
            'name' => new FieldSettings('name', FieldType::String),
            'space' => new FieldSettings('space', FieldType::Cast),
            'statuses' => new FieldSettings('statuses', FieldType::Cast, true),
            'lists' => new FieldSettings('lists', FieldType::Cast, true),
            'private' => new FieldSettings('private', FieldType::Bool),
            'permission_level' => new FieldSettings('permission_level', FieldType::String),
            'archived' => new FieldSettings('archived', FieldType::Bool),
            'hidden' => new FieldSettings('hidden', FieldType::Bool),
            'override_statuses' => new FieldSettings('override_statuses', FieldType::Bool),
        ]);
    }

    public function lists(): QueryBuilderInterface
    {
        return $this->selectChildren(Listing::class);
    }

    public function tasks(): QueryBuilderInterface
    {
        return $this->selectChildren(Task::class);
    }
}