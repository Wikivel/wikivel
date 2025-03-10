<?php

namespace App\Helpers\Nestedset;

use Illuminate\Database\Schema\Blueprint;

class NestedSet
{
    /**
     * The name of default lft column.
     */
    const LFT = 'left_id';

    /**
     * The name of default rgt column.
     */
    const RGT = 'right_id';

    /**
     * The name of default parent id column.
     */
    const PARENT_ID = 'parent_id';

    /**
     * Insert direction.
     */
    const BEFORE = 1;

    /**
     * Insert direction.
     */
    const AFTER = 2;

    /**
     * Add default nested set columns to the table. Also create an index.
     *
     * @param \Illuminate\Database\Schema\Blueprint $table
     */
    public static function columns(Blueprint $table)
    {
        $table->unsignedBigInteger(self::LFT)->default(0);
        $table->unsignedBigInteger(self::RGT)->default(0);
        $table->unsignedBigInteger(self::PARENT_ID)->nullable();

        $table->index(static::getDefaultColumns());
    }

    /**
     * Drop NestedSet columns.
     *
     * @param \Illuminate\Database\Schema\Blueprint $table
     */
    public static function dropColumns(Blueprint $table)
    {
        $columns = static::getDefaultColumns();

        $table->dropIndex($columns);
        $table->dropColumn($columns);
    }

    /**
     * Get a list of default columns.
     *
     * @return array
     */
    public static function getDefaultColumns()
    {
        return [ static::LFT, static::RGT, static::PARENT_ID ];
    }

    /**
     * Replaces instanceof calls for this trait.
     *
     * @param mixed $node
     *
     * @return bool
     */
    public static function isNode($node)
    {
        return is_object($node) && in_array(NodeTrait::class, (array)$node);
    }
}
