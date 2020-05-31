<?php

require 'init.php';

/*
$app->add(['Button', 'View Form input split in Accordion section', 'small right floated basic blue', 'iconRight' => 'right arrow'])
    ->link(['accordion-in-form']);
$app->add(['ui' => 'clearing divider']);
*/

$app->add(['Header', 'Nested accordions']);

function addAccordion($view, $max_depth = 2, $level = 0)
{
    $accordion = $view->add(['Accordion', 'type' => ['styled', 'fluid']]);

    // static section
    $i1 = $accordion->addSection('Static Text');
    $i1->add(['Message', 'This content is added on page loaded', 'ui' => 'tiny message']);
    $i1->add(['LoremIpsum', 'size' => 1]);
    if ($level < $max_depth) {
        addAccordion($i1, $max_depth, $level + 1);
    }

    // dynamic section - simple view
    $i2 = $accordion->addSection('Dynamic Text', function ($v) use ($max_depth, $level) {
        $v->add(['Message', 'Every time you open this accordion item, you will see a different text', 'ui' => 'tiny message']);
        $v->add(['LoremIpsum', 'size' => 2]);
        if ($level < $max_depth) {
            addAccordion($v, $max_depth, $level + 1);
        }
    });

    // dynamic section - form view
    $i3 = $accordion->addSection('Dynamic Form', function ($v) use ($max_depth, $level) {
        $v->add(['Message', 'Loading a form dynamically.', 'ui' => 'tiny message']);
        $f = $v->add(['Form']);
        $f->addField('Email');
        $f->onSubmit(function ($form) {
            return $form->success('Subscribed '.$form->model['Email'].' to newsletter.');
        });

        if ($level < $max_depth) {
            addAccordion($v, $max_depth, $level + 1);
        }
    });
}

// add accordion structure
$a = addAccordion($app);
