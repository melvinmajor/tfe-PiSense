<?php

namespace acct;

class Supplier extends Contact {
    function init()
    {
        parent::init();
        $this->addCondition('type','supplier');
    }
}
