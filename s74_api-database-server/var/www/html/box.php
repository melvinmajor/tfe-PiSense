<?php
include_once('init.php');

$app->add(['CRUD', 'paginator'=>false])->setModel(new Box($db, 'Box'));

