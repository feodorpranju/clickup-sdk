<?php


namespace Feodorpranju\ClickUp\SDK\Models\Fields;


class DateTimeField extends \Feodorpranju\ApiOrm\Models\Fields\DateTimeField
{
    protected function toApi(mixed $value): ?string
    {
        return $this->toUsable($value)->format('U') * 1000;
    }
}