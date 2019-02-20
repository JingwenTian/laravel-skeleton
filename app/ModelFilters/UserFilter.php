<?php
/**
 * user 条件处理.
 */

namespace App\ModelFilters;

/**
 * Class UserFilter.
 *
 * @package App\ModelFilters
 */
class UserFilter extends AbstractFilters
{
    public function id(int $id)
    {
        return $this->where('id', $id);
    }

    public function idGte(int $id)
    {
        return $this->where('id', '>=', $id);
    }

    public function name(string $name)
    {
        return $this->where('name', $name);
    }

    public function email(string $email)
    {
        return $this->where('email', $email);
    }
}
