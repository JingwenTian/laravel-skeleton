<?php

/**
 * sample mongo 条件处理.
 */

namespace App\ModelFilters;

use Carbon\Carbon;

/**
 * Class SampleMongoModelFilter.
 *
 * @package App\ModelFilters
 */
class SampleMongoModelFilter extends AbstractFilters
{
    // ....

    public function status(int $status)
    {
        return $this->where('status', $status);
    }

    public function createdAtGte(string $date)
    {
        return $this->where('created_at', '>=', Carbon::createFromTimeString($date));
    }

    public function createdAtLte(string $date)
    {
        return $this->where('created_at', '<=', Carbon::createFromTimeString($date));
    }

    public function updatedAtGte(string $date)
    {
        return $this->where('updated_at', '>=', Carbon::createFromTimeString($date));
    }

    public function updatedAtLte(string $date)
    {
        return $this->where('updated_at', '<=', Carbon::createFromTimeString($date));
    }
}
