<?php

namespace atk4\ui\TableColumn;

/**
 * Implements Column helper for grid.
 */
class Password extends Generic
{
    public function getDataCellTemplate(\atk4\data\Field $f = null)
    {
        return '***';
    }
}
