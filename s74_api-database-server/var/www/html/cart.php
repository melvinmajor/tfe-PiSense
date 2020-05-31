<?php 

include_once('init.php');

$grid = new \atk4\ui\Table();
$data = new User($db);
#data->addCondition('is_new', true);
#data->addCondition('client_id', $_GET['client_id']);
$grid->setModel($data);

$html = $grid->render();


