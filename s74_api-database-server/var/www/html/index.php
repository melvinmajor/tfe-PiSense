<?php

require 'vendor/autoload.php';

$app = new \atk4\ui\App('Welcome to Agile Toolkit');
$app->initLayout('Centered');


/****************************************************************
 * You can now remove the text below and write your own Web App *
 *                                                              *
 * Thank you for trying out Agile Toolkit                       *
 ****************************************************************/


$app->add('Text')
    ->addParagraph('You have successfully installed Agile Toolkit '.$app->version)
    ->addParagraph('welcome.');

$app->add(['Button', 'icon'=>'dashboard'])
    ->addClass('primary')
    ->set('Admin')
    ->link(['admin']);

$app->add(['Button', 'icon'=>'lightning'])
    ->set('Learn NOW!')
    ->setAttr('target', '_blank')
    ->link('https://www.agiletoolkit.org/school');

$app->add(['Button', 'icon'=>'book'])
    ->set('Documentation')
    ->setAttr('target', '_blank')
    ->link('http://agile-ui.readthedocs.io/');

$app->add(['Button', 'icon'=>'trophy'])
    ->set('Examples')
    ->setAttr('target', '_blank')
    ->link('http://ui.agiletoolkit.org/');

$app->add(['Button', 'icon'=>'cogs'])
    ->set('ATK Data')
    ->setAttr('target', '_blank')
    ->link('https://git.io/ad');
