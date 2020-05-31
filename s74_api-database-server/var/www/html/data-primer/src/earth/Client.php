<?php

namespace earth;

class Client extends \atk4\data\Model
{
    public $table = 'client';
    function init()
    {
        parent::init();

        $this->addFields(['name','address']);

        $this->hasMany('Project', new Project());

        $this->addExpression('earnings', $this->expr('coalesce([],0)',[
            $this->refLink('Project')->addCondition('is_cancelled', false)->action('fx', ['sum', 'budget'])
        ]));
    }
}
