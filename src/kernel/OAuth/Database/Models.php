<?php

namespace Lxh\OAuth\Database;

/**
 *
 * @author Jqh
 * @date   2018/3/15 14:15
 */
class Models
{
    /**
     * Map for the bouncer's models.
     *
     * @var array
     */
    protected static $models = [];

    protected static $tables = [];

    /**
     * Get a custom table name mapping for the given table.
     *
     * @param  string  $table
     * @return string
     */
    public static function table($table)
    {
        if (isset(static::$tables[$table])) {
            return static::$tables[$table];
        }

        return $table;
    }
}
