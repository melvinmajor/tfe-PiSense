<?php

namespace acct;

class Invoice extends Document {

    function init()
    {
        parent::init();
        $this->addCondition('type','invoice');

        $this->join('invoice.document_id')
            ->addFields(['purchase','shipping']);

        $this->addExpression('total','[purchase]+[shipping]');
    }
}
