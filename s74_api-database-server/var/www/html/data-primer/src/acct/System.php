<?php

namespace acct;

class System extends \atk4\data\Model
{
    public $table = 'system';

    function init() {
        parent::init();

        $this->addField('name', ['actual'=>'legal_name', 'type'=>'string']);

        $this->hasOne('owner_user_id', new User());

        $this->hasMany('Client', new Client());
        $this->hasMany('Supplier', new Supplier());
    }
}
