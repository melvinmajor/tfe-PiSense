<?php

namespace inv;

class InvoiceLine extends \atk4\data\Model {
    public $table = 'inv_invoice_line';
    public $title_field = 'item';

    function init() {
        parent::init();

        $this->hasOne('inv_invoice_id', new Invoice());

        $this->addField('item', ['type'=>'string']);
        $this->addField('qty', ['type'=>'integer']);
        $this->addField('price', ['type'=>'money']);

        $this->addExpression('total', '[price]*[qty]');
    }
}
