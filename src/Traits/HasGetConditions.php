<?php


namespace Feodorpranju\ClickUp\SDK\Traits;


use Carbon\Carbon;
use Feodorpranju\ClickUp\SDK\Models\Fields\FieldSettings;
use Illuminate\Support\Collection;
use Feodorpranju\ApiOrm\Enumerations\FieldType;
use Feodorpranju\ApiOrm\Enumerations\FieldGetMode;
use function Symfony\Component\Translation\t;

trait HasGetConditions
{
    protected function generateSelectData(Collection $conditions): array
    {
        $data = [];

        $conditions = $this->prepareConditions($conditions);

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

    /**
     * @param Collection $conditions
     * @return Collection
     */
    protected function prepareConditions(Collection $conditions): Collection
    {
        $conditions = $conditions->map(fn($cond) => [
            $cond[0],
            $cond[1],
            is_object($cond[2]) && is_a($cond[2], Carbon::class)
                ? $cond[2]->getTimestampMs()
                : $cond[2]
        ]);

        if (defined(static::class.'::ARRAY_TO_STRING_FILTERS')) {
            $conditions = $conditions->map(fn($cond) => [
                $cond[0],
                $cond[1],
                is_array($cond[2]) && in_array($cond[0], static::ARRAY_TO_STRING_FILTERS)
                    ? join(',', $cond[2])
                    : $cond[2]
            ]);
        }

        return $conditions;
    }

    /**
     * Removes GET filters from conditions
     *
     * @param Collection $conditions
     * @return Collection
     */
    protected function filterAfterSelectConditions(Collection $conditions): Collection
    {
        if (!defined(static::class.'::GET_FILTERS')) {
            return $conditions;
        }

        return $conditions->filter(fn($cond) => !in_array($cond[0], static::GET_FILTERS));
    }
}