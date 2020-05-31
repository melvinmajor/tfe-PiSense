<?php

namespace inv;

class Client extends \atk4\data\Model {
    public $table = 'inv_client';

    function init() {
        parent::init();

        $this->addField('name');

        $this->hasMany('Invoice', new Invoice())
            ->addField('total', ['aggregate'=>'sum']);
    }
}
