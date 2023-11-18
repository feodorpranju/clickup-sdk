<?php


namespace Feodorpranju\ClickUp\SDK\Models;


use Feodorpranju\ApiOrm\Contracts\QueryBuilderInterface;
use Feodorpranju\ClickUp\SDK\Models\Fields\FieldSettings;
use Feodorpranju\ClickUp\SDK\Traits\HasChildren;
use Feodorpranju\ClickUp\SDK\Traits\HasParent;
use Illuminate\Support\Collection;
use Feodorpranju\ApiOrm\Enumerations\FieldType;

class Listing extends AbstractModel
{
    use HasParent, HasChildren;

    public const ENTITY_NAME = 'list';

    /**
     * For HasParent trait
     */
    public const PARENT = Folder::class;
    public const GET_BY_PARENT_PATTERN = 'folder/?/list';
    public const EXISTS_IN_PARENT = true;
    public const GET_FILTERS = ['archived'];

    public function fields(): Collection
    {
        return collect([
            'id' => new FieldSettings('id', FieldType::Int),
            'folder_id' => new FieldSettings('space_id', FieldType::Int),
            'task_count' => new FieldSettings('task_count', FieldType::Int),
            'orderindex' => new FieldSettings('orderindex', FieldType::Int),
            'name' => new FieldSettings('name', FieldType::String),
            'content' => new FieldSettings('content', FieldType::String),
            'priority' => new FieldSettings('priority', FieldType::Cast),
            'assignee' => new FieldSettings('assignee', FieldType::Cast),
            'space' => new FieldSettings('space', FieldType::Cast),
            'folder' => new FieldSettings('folder', FieldType::Cast),
            'private' => new FieldSettings('private', FieldType::Bool),
            'permission_level' => new FieldSettings('permission_level', FieldType::String),
            'archived' => new FieldSettings('archived', FieldType::Bool),
            'hidden' => new FieldSettings('hidden', FieldType::Bool),
            'override_statuses' => new FieldSettings('override_statuses', FieldType::Bool),
            'due_date' => new FieldSettings('due_date', FieldType::Datetime),
            'start_date' => new FieldSettings('start_date', FieldType::Datetime),
        ]);
    }

    public function tasks(): QueryBuilderInterface
    {
        return $this->selectChildren(Task::class);
    }
}