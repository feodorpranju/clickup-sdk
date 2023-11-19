<?php


namespace Feodorpranju\ClickUp\SDK\Models;


use Feodorpranju\ClickUp\SDK\Models\Fields\FieldSettings;
use Feodorpranju\ClickUp\SDK\Traits\HasParent;
use Illuminate\Support\Collection;
use Feodorpranju\ApiOrm\Enumerations\FieldType;

class Task extends AbstractModel
{
    use HasParent;

    public const ENTITY_NAME = 'task';

    /**
     * For HasParent trait
     */
    public const PARENT = Listing::class;
    public const GET_BY_PARENT_PATTERN = 'list/?/task';
    public const EXISTS_IN_PARENT = false;
    public const GET_FILTERS = [
        'archived',
        'page',
        'order_by',
        'reverse',
        'subtasks',
        'statuses',
        'include_closed',
        'assignees',
        'tags',
        'due_date_gt',
        'due_date_lt',
        'date_created_gt',
        'date_created_lt',
        'date_updated_gt',
        'date_updated_lt',
        'date_done_gt',
        'date_done_lt',
        'custom_fields',
    ];

    protected const FILTERABLE_DATE_FIELDS = [
        'due_date',
        'date_created',
        'date_updated',
        'date_done',
    ];

    public function fields(): Collection
    {
        return collect([
            'id' => new FieldSettings('id', FieldType::String),
            'list_id' => new FieldSettings('space_id', FieldType::Int),
            'team_id' => new FieldSettings('team_id', FieldType::Int),
            'orderindex' => new FieldSettings('orderindex', FieldType::Int),
            'name' => new FieldSettings('name', FieldType::String),
            'custom_id' => new FieldSettings('custom_id', FieldType::String),
            'text_content' => new FieldSettings('text_content', FieldType::String),
            'description' => new FieldSettings('description', FieldType::String),
            'priority' => new FieldSettings('priority', FieldType::Cast),
            'creator' => new FieldSettings('creator', FieldType::Cast, true),
            'assignees' => new FieldSettings('assignees', FieldType::Cast, true),
            'dependencies' => new FieldSettings('dependencies', FieldType::Cast, true),
            'linked_tasks' => new FieldSettings('linked_tasks', FieldType::Cast, true),
            'space' => new FieldSettings('space', FieldType::Cast),
            'folder' => new FieldSettings('folder', FieldType::Cast),
            'list' => new FieldSettings('list', FieldType::Cast),
            'project' => new FieldSettings('project', FieldType::Cast),
            'parent' => new FieldSettings('parent', FieldType::Cast),
            'points' => new FieldSettings('points', FieldType::Cast),
            'custom_fields' => new FieldSettings('custom_fields', FieldType::Cast, true),
            'tags' => new FieldSettings('tags', FieldType::Cast, true),
            'checklists' => new FieldSettings('checklists', FieldType::Cast, true),
            'watchers' => new FieldSettings('watchers', FieldType::Cast, true),
            'sharing' => new FieldSettings('sharing', FieldType::Cast, true),
            'private' => new FieldSettings('private', FieldType::Bool),
            'permission_level' => new FieldSettings('permission_level', FieldType::String),
            'archived' => new FieldSettings('archived', FieldType::Bool),
            'hidden' => new FieldSettings('hidden', FieldType::Bool),
            'override_statuses' => new FieldSettings('override_statuses', FieldType::Bool),
            'due_date' => new FieldSettings('due_date', FieldType::Datetime),
            'start_date' => new FieldSettings('start_date', FieldType::Datetime),
            'date_done' => new FieldSettings('date_done', FieldType::Datetime),
            'date_created' => new FieldSettings('date_created', FieldType::Datetime),
            'date_updated' => new FieldSettings('date_updated', FieldType::Datetime),
            'date_closed' => new FieldSettings('date_closed', FieldType::Datetime),
            'time_estimate' => new FieldSettings('time_estimate', FieldType::Time),
            'url' => new FieldSettings('url', FieldType::Url),
        ]);
    }
}