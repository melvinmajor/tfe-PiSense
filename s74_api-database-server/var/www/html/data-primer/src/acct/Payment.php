<?php

namespace acct;

class Payment extends Document {

    function init()
    {
        parent::init();
        $this->addCondition('type','payment');

        $this->join('payment.document_id')
            ->addFields(['amount']);
    }
}
