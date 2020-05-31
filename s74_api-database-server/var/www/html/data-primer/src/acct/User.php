<?php

namespace acct;

class User extends \atk4\data\Model
{
    public $table = 'user';

    function init() {
        parent::init();

        $this->addField('username', ['type'=>'string']);
        $this->addField('email', ['type'=>'string']);
        $this->addExpression('name', 'concat([username], " (",[email],")');

        $j = $this->join('contact_info', 'contact_info_id');
        $j->addField('address_1');
        $j->addField('address_2');
        $j->addField('address_3');
        $j->hasOne('country_id', 'Country');
    }
}
