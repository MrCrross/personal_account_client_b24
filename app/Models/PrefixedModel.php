<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

abstract class PrefixedModel extends Model
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = $this->getTableWithPrefix();
    }

    protected function getTableWithPrefix(): string
    {
        $table = $this->getTable();
        $prefix = config('database.prefix', '');

        // Если префикс еще не добавлен - добавляем
        if ($prefix && !str_starts_with($table, $prefix)) {
            return $prefix . $table;
        }

        return $table;
    }
}
