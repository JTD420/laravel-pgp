<?php

namespace JTD420\PGP\Console;

use Illuminate\Support\Str;

/**
 * Credits to : https://stackoverflow.com/a/57337748
 */
trait ModelTrait
{
    /**
     * Scopped Variables
     */
    protected $table_prefix;

    /**
     * Appends prefix to table name
     *
     * @return $table
     */
    public function getTable()
    {
        if (class_basename($this) == 'User') {
            return parent::getTable();
        }

        $this->table_prefix = config('PGP.table_prefix');
        $model = str::snake(class_basename($this));

        if (!isset($this->table)) {
            $this->setTable(Str::plural($this->table_prefix . $model));
        }

        return $this->table;
    }

    /**
     * Get the table prefix for the model.
     *
     * @return string
     */
    public function getTablePrefix()
    {
        return config('PGP.table_prefix');
    }
}
