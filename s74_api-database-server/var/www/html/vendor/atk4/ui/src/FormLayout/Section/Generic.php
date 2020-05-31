<?php

namespace atk4\ui\FormLayout\Section;

class Generic extends \atk4\ui\View
{
    public $formLayout = 'FormLayout/Generic';
    public $form = null;

    /**
     * Adds sub-layout in existing layout.
     *
     * @return \atk4\ui\FormLayout\Generic
     */
    public function addSection()
    {
        return $this->add([$this->formLayout, 'form' => $this->form]);
    }
}
