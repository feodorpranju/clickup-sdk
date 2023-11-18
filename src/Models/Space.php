<?php


namespace Feodorpranju\ClickUp\SDK\Models;


use Feodorpranju\ApiOrm\Contracts\QueryBuilderInterface;
use Feodorpranju\ClickUp\SDK\Models\Fields\FieldSettings;
use Feodorpranju\ClickUp\SDK\Traits\HasChildren;
use Feodorpranju\ClickUp\SDK\Traits\HasParent;
use Illuminate\Support\Collection;
use Feodorpranju\ApiOrm\Enumerations\FieldType;

class Space extends AbstractModel
{
    use HasParent, HasChildren;

    public const ENTITY_NAME = 'space';

    /**
     * For HasParent trait
     */
    public const PARENT = Workspace::class;
    public const GET_BY_PARENT_PATTERN = 'team/?/space';
    public const EXISTS_IN_PARENT = false;
    public const GET_FILTERS = ['archived'];

    public function fields(): Collection
    {
        return collect([
            'id' => new FieldSettings('id', FieldType::Int),
            'workspace_id' => new FieldSettings('workspace_id', FieldType::Int),
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
}