<?php


namespace Feodorpranju\ClickUp\SDK\Traits;


use Feodorpranju\ClickUp\SDK\Models\Fields\FieldSettings;
use Feodorpranju\ClickUp\SDK\Models\Task;
use Illuminate\Support\Collection;
use Feodorpranju\ApiOrm\Enumerations\FieldType;
use Feodorpranju\ApiOrm\Enumerations\FieldGetMode;

trait HasGetConditions
{
    protected function generateSelectData(Collection $conditions): array
    {
        $data = [];

        foreach ($conditions as $idx => $condition) {
            if (in_array($condition[0], static::GET_FILTERS)) {
                if (in_array($condition[1], ['', '=', '=='])) {
                    $data[$condition[0]] = $condition[2];
                }

                $conditions->forget($idx);
            }
        }

        $data = array_merge(
            $data,
            $this->generateDateSelectData($conditions)
        );

        if (static::class === Task::class) {
            dd($data);
        }

        return $data;
    }

    /**
     * Generates conditions for dates
     *
     * @param Collection $conditions
     * @return array
     */
    protected function generateDateSelectData(Collection $conditions): array
    {
        if (!defined(static::class.'::FILTERABLE_DATE_FIELDS')) {
            return [];
        }

        $data = [];

        foreach ($conditions as $idx => $condition) {
            if (
                !in_array($condition[1], ['>', '>=', '<', '<=', '=', '==', ''])
                || !in_array($condition[0], static::FILTERABLE_DATE_FIELDS)
            ) {
                continue;
            }

            $value = $this->dateToApi($condition[2]);
            $key = $condition[0];

            switch ($condition[1]) {
                case '>=':
                    $value--;
                case '>':
                    $data[$key.'_gt'] = $value;
                    break;
                case '<=':
                    $value++;
                case '<':
                    $data[$key.'_lt'] = $value;
                    break;
                case '':
                case '=':
                case '==':
                    $data[$key.'_gt'] = $value - 1000;
                    $data[$key.'_lt'] = $value + 1000;
                    break;
            }

            unset($conditions);
        }

        return $data;
    }

    /**
     * Converts date to Api format
     *
     * @param mixed $date
     * @return int
     */
    protected function dateToApi(mixed $date): int
    {
        $fieldSettings = new FieldSettings('', FieldType::Datetime);
        return $fieldSettings->field($date)->get(FieldGetMode::Api);
    }
}