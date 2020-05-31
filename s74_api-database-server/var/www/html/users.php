<?php

require 'vendor/autoload.php';


$app = new \atk4\ui\App('Welcome to Agile Toolkit');
$app->initLayout('Admin');


/****************************************************************
 * You can now remove the text below and write your own Web App *
 *                                                              *
 * Thank you for trying out Agile Toolkit                       *
 ****************************************************************/

// Default installation gives warning, so update php.ini the remove this line
date_default_timezone_set('UTC');

$app->layout->leftMenu->addItem(['Page d\'introduction', 'icon'=>'puzzle'], ['index']);
$app->layout->leftMenu->addItem(['Liste des clients', 'icon'=>'dashboard'], ['admin']);
$app->layout->leftMenu->addItem(['Liste des box', 'icon'=>'dashboard'], ['box']);
$app->layout->leftMenu->addItem(['Liste des utilisateurs', 'icon'=>'dashboard'], ['users']);

class Box extends \atk4\data\Model {
    function init() {
        parent::init();
#    userID 	mail 	password 	name 	firstname 	address 	phone 	birthdate 	dateRegistered 	device 	deviceOutdoor 	sensors 
        $this->addField('userID');
        $this->addField('password');
	$this->addField('mail');
        $this->addField('name');
        $this->addField('firstname');
        $this->addField('address');
        $this->addField('phone');
        $this->addField('birthdate', ['type'=>'date']);
        $this->addField('dateRegistered');
        $this->addField('device');
        $this->addField('deviceOutdoor');
	$this->addField('sensors');

    }
}

session_start();
$db = new \atk4\data\Persistence_Array($_SESSION);
# Inject correct password here under after 'atk4_test' before launching
#$db = new \atk4\data\Persistence\SQL('mysql:dbname=atk4_test;host=localhost', 'atk4_test');
$db = \atk4\data\Persistence::connect('mysql:dbname=atk4_test;host=localhost', 'atk4_test');
$app->add(['CRUD', 'paginator'=>false])->setModel(new Box($db, 'Box'));

