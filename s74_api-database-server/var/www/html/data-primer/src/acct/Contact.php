<?php

namespace acct;

class Contact extends \atk4\data\Model
{
    public $table = 'contact';

    function init() {
        parent::init();

        $this->add(new Field_System());

        $this->addField('name', ['type'=>'string']);
        $this->addField('type', ['enum'=>['client','supplier']]);
        $this->addField('status', ['enum'=>['active','suspended']]);

        $this->hasMany('Invoice', new Invoice());
        $this->hasMany('Payment', new Payment());

        $j = $this->join('contact_info', 'contact_info_id');
        $j->addField('address_1');
        $j->addField('address_2');
        $j->addField('address_3');
        $j->hasOne('country_id', new Country())
            ->addField('country', 'name');
    }
}
