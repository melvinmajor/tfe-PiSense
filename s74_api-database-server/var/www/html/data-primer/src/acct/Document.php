<?php

namespace acct;

class Document extends \atk4\data\Model
{
    public $table = 'document';

    function init() {
        parent::init();

        $this->addField('ref_no', ['type'=>'string']);
        $this->addField('date', ['type'=>'date']);
        $this->addField('type', ['enum'=>['invoice','payment']]);

        $this->hasOne('contact_id', new Contact());

        $this->addField('is_archived', ['type'=>'boolean']);

    }

}
