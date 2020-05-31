<?php

namespace earth;

class VIPClient extends Client
{
    function init()
    {
        parent::init();

        $this->addCondition('earnings','>',1000);
    }



}
