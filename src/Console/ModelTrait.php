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
        $this->table_prefix = config('PGP.table_prefix');

        $model = explode('\\', get_class($this));
        $model = Str::lower(array_pop($model));

        if (! isset($this->table)) {
            $this->setTable(Str::plural($this->table_prefix.$model));
        }

        return $this->table;
    }
}
