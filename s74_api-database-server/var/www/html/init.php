<?php 
require 'vendor/autoload.php';
$app = new \atk4\ui\App('Welcome to Agile Toolkit');
$app->initLayout('Admin');
$db = new \atk4\data\Persistence_Array($_SESSION);
# Inject correct password here under after 'atk4_test' before launching
#$db = new \atk4\data\Persistence\SQL('mysql:dbname=atk4_test;host=localhost', 'atk4_test');
$db = \atk4\data\Persistence::connect('mysql:dbname=atk4_test;host=localhost', 'atk4_test');


/****************************************************************
 * You can now remove the text below and write your own Web App *
 *                                                              *
 * Thank you for trying out Agile Toolkit                       *
 ****************************************************************/

// Default installation gives warning, so update php.ini the remove this line
date_default_timezone_set('UTC');

#$app->layout->leftMenu->addItem(['Front-end demo', 'icon'=>'puzzle'], ['index']);
#$app->layout->leftMenu->addItem(['Admin demo', 'icon'=>'dashboard'], ['admin']);
#$app->layout->leftMenu->addItem(['Liste des box', 'icon'=>'dashboard'], ['box']);
$app->layout->leftMenu->addItem(['Home', 'icon'=>'puzzle'], ['index']);
$app->layout->leftMenu->addItem(['Users', 'icon'=>'dashboard'], ['admin']);
$app->layout->leftMenu->addItem(['Datas', 'icon'=>'dashboard'], ['box']);

$app->layout->leftMenu->addItem(['login', 'icon'=>'dashboard'], ['login']);

class TestClient extends \atk4\data\Model {
    function init() {
        parent::init();

        $this->addField('full_name');
        $this->addField('company');
        $this->addField('added', ['type'=>'date']);
        $this->addField('balance', ['type'=>'money']);
    }
}
class User extends \atk4\data\Model {
    public $table = 'user';

    function init()
    {
        parent::init();
	$this->addField('firstname');
        $this->addField('name');
        $this->addField('email');
        $this->addField('is_admin', ['type'=>'boolean']);
        $this->addField('password', ['\atk4\login\Field\Password']);

        $this->addField('address');
        $this->addField('phone');
        $this->addField('birthdate',['type'=>'date']);
        $this->addField('dateRegistered');
        $this->addField('device');
        $this->addField('deviceOutdoor');
        $this->addField('sensors');
    }
}

class Box extends \atk4\data\Model {
    public $table = 'Box';

    function init()
    {
        parent::init();

        $this->addField('boxID');
        $this->addField('datetime');
        $this->addField('temperature');
$this->addField('humidity');
$this->addField('pressure');
$this->addField('gas');
$this->addField('PM2');
$this->addField('PM10');
    }
}

session_start();
$app->add(new \atk4\login\Auth())
    ->setModel(new User($db));


