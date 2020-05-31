<?php

require 'init.php';

$app->add(new \atk4\ui\View([
    'Forms below demonstrate how to work with multi-value selectors',
    'ui' => 'ignored warning message',
]));

$cc = $app->add('Columns');
$f = $cc->addColumn()->add(new \atk4\ui\Form());

$f->addField('one', null, ['enum'=>['female', 'male']])->set('male');
$f->addField('two', ['Radio'], ['enum'=>['female', 'male']])->set('male');

$f->addField('three', null, ['values'=>['female', 'male']])->set(1);
$f->addField('four', ['Radio'], ['values'=>['female', 'male']])->set(1);

$f->addField('five', null, ['values'=>[5=>'female', 7=>'male']])->set(7);
$f->addField('six', ['Radio'], ['values'=>[5=>'female', 7=>'male']])->set(7);

$f->addField('seven', null, ['values'=>['F'=>'female', 'M'=>'male']])->set('M');
$f->addField('eight', ['Radio'], ['values'=>['F'=>'female', 'M'=>'male']])->set('M');

$f->onSubmit(function ($f) {
    echo json_encode($f->model->get());
});
