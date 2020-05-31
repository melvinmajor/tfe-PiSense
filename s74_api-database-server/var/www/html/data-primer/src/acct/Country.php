<?php

namespace acct;

class Country extends \atk4\data\Model
{
    public $table = 'country';

    function init() {
        parent::init();

        $this->addField('name', ['type'=>'string']);
    }
}
