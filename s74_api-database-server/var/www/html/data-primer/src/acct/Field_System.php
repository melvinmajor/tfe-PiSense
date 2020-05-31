<?php

namespace acct;

class Field_System extends \atk4\data\Reference_SQL_One
{
    public $link = 'system_id';

    function init() {
        parent::init();
        $this->model = new System();
        $this->addTitle();
    }
}
