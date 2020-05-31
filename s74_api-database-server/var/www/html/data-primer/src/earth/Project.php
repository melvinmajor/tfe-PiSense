<?php

namespace earth;

class Project extends \atk4\data\Model
{
    public $table = 'project';
    function init()
    {
        parent::init();

        $this->addField('name');
        $this->addField('budget', ['type'=>'money']);
        $this->addField('is_cancelled', ['type'=>'boolean']);

        $this->hasOne('client_id', new Client());
    }
}
