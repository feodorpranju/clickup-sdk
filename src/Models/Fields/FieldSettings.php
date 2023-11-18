<?php


namespace Feodorpranju\ClickUp\SDK\Models\Fields;


use Feodorpranju\ApiOrm\Models\Fields\Settings;

class FieldSettings extends Settings
{
    protected const FIELD_TYPES = [
        "datetime" => DateTimeField::class,
        "date" => DateTimeField::class,
        "time" => DateTimeField::class,
    ];
}