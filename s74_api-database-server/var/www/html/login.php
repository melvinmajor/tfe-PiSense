<?php

require 'vendor/autoload.php';


$app = new \atk4\ui\App('Welcome to Agile Toolkit');
$app->initLayout('Admin');



class User extends \atk4\data\Model {
    public $table = 'User';

    function init()
    {
        parent::init();

        $this->addField('name');
        $this->addField('email');
        $this->addField('is_admin', ['type'=>'boolean']);
        $this->addField('password', ['\atk4\login\Field\Password']);
    }
}
# Inject correct password here under after 'atk4_test' before launching
$db = \atk4\data\Persistence::connect('mysql:dbname=atk4_test;host=localhost', 'atk4_test');
$app->add(new \atk4\login\RegisterForm())
    ->setModel(new \atk4\login\Model\User($db));
#$app->add(['CRUD', 'paginator'=>false])->setModel(new Box($db, 'Box'));

// ADD THIS CODE:
#$app->add(new \atk4\login\Auth())
#    ->setModel(new User($app->db));

// The rest of YOUR UI code will now be protected
