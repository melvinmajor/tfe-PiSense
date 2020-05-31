<?php

namespace inv;

class Invoice extends \atk4\data\Model {
    public $table = 'inv_invoice';

    function init() {
        parent::init();

        $this->hasOne('inv_client_id', new Client())
            ->addField('client', 'name');

        $this->addExpression('name', 'concat([client], " #", [ref])');

        $this->addField('ref');
        $this->addField('created', ['type'=>'date', 'default'=>new \DateTime()]);

        $this->hasMany('Line', new InvoiceLine())
            ->addField('total', ['aggregate'=>'sum']);
    }
}
