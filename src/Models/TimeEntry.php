<?php


namespace Feodorpranju\ClickUp\SDK\Models;


use Feodorpranju\ApiOrm\Contracts\Http\ApiClientInterface;
use Feodorpranju\ClickUp\SDK\Traits\HasParent;

/**
 * @method static withClient(ApiClientInterface $client)
 */
class TimeEntry extends AbstractModel
{
    use HasParent;

    public const ENTITY_NAME = 'time_entry';

    /**
     * For HasParent trait
     */
    public const PARENT = Workspace::class;
    public const GET_BY_PARENT_PATTERN = 'team/?/time_entries';
    public const FIND_RESULT_KEY = 'data';
    public const EXISTS_IN_PARENT = false;
    public const GET_FILTERS = [
        'start_date',
        'end_date',
        'assignee',
        'include_task_tags',
        'include_location_names',
        'space_id',
        'folder_id',
        'list_id',
        'task_id',
        'custom_task_ids',
        'team_id',
    ];
    public const ARRAY_TO_STRING_FILTERS = [
        'assignee'
    ];
}