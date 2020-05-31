<?php

namespace atk4\ui\FormLayout;

use atk4\ui\Form;

/**
 * Generic Layout for a form.
 */
class Generic extends _Abstract
{
    /** {@inheritdoc} */
    public $defaultTemplate = 'formlayout/generic.html';

    /**
     * If specified will appear on top of the group. Can be string or Label object.
     *
     * @var string
     */
    public $label = null;

    /**
     * Specify width of a group in numerical word e.g. 'width'=>'two' as per
     * Semantic UI grid system.
     *
     * @var string
     */
    public $width = null;

    /**
     * Set true if you want fields to appear in-line.
     *
     * @var bool
     */
    public $inline = false;

    protected function _addField($decorator, $field)
    {
        return $this->_add($decorator, ['desired_name' => $field->short_name]);
    }

    /**
     * Adds Button.
     *
     * @param Button|array|string $seed
     *
     * @return \atk4\ui\Button
     */
    public function addButton($seed)
    {
        return $this->add($this->mergeSeeds(['Button'], $seed), 'Buttons');
    }

    /**
     * Adds Header in form layout.
     *
     * @param string $label
     *
     * @return $this
     */
    public function addHeader($label)
    {
        $this->add(['Header', $label, 'dividing', 'element' => 'h4']);

        return $this;
    }

    /**
     * Adds field group in form layout.
     *
     * @param string|array $label
     *
     * @return static
     */
    public function addGroup($label = null)
    {
        if (!is_array($label)) {
            $label = ['label' => $label];
        } elseif (isset($label[0])) {
            $label['label'] = $label[0];
            unset($label[0]);
        }

        $label['form'] = $this->form;

        return $this->add(new static($label));
    }

    /**
     * Add a form layout section to this layout.
     *
     * Each section may contain other section or group.
     *
     * @param mixed $seed
     * @param bool  $addDivider Should we add divider after this section
     *
     * @throws \atk4\core\Exception
     * @throws \atk4\ui\Exception
     *
     * @return static
     */
    public function addSubLayout($seed = 'Generic', $addDivider = true)
    {
        $v = $this->add($this->factory($seed, ['form' => $this->form], 'FormLayout/Section'));
        if ($v instanceof \atk4\ui\FormLayout\Section\Generic) {
            $v = $v->addSection();
        }

        if ($addDivider) {
            $this->add(['ui' => 'hidden divider']);
        }

        return $v;
    }

    /**
     * Recursively renders this view.
     */
    public function recursiveRender()
    {
        $field_input = $this->template->cloneRegion('InputField');
        $field_no_label = $this->template->cloneRegion('InputNoLabel');
        $labeled_group = $this->template->cloneRegion('LabeledGroup');
        $no_label_group = $this->template->cloneRegion('NoLabelGroup');

        $this->template->del('Content');

        foreach ($this->elements as $el) {

            // Buttons go under Button section
            if ($el instanceof \atk4\ui\Button) {
                $this->template->appendHTML('Buttons', $el->getHTML());
                continue;
            }

            if ($el instanceof \atk4\ui\FormLayout\Generic) {
                if ($el->label && !$el->inline) {
                    $template = $labeled_group;
                    $template->set('label', $el->label);
                } else {
                    $template = $no_label_group;
                }

                if ($el->width) {
                    $template->set('width', $el->width);
                }

                if ($el->inline) {
                    $template->set('class', 'inline');
                }
                $template->setHTML('Content', $el->getHTML());

                $this->template->appendHTML('Content', $template->render());
                continue;
            }

            // Anything but fields or explicitly defined fields gets inserted directly
            if (!$el instanceof \atk4\ui\FormField\Generic || !$el->layoutWrap) {
                $this->template->appendHTML('Content', $el->getHTML());
                continue;
            }

            $template = $field_input;
            $label = $el->caption ?: $el->field->getCaption();

            // Anything but fields gets inserted directly
            if ($el instanceof \atk4\ui\FormField\CheckBox) {
                $template = $field_no_label;
                $el->template->set('Content', $label);
                /*
                $el->addClass('field');
                $this->template->appendHTML('Fields', '<div class="field">'.$el->getHTML().'</div>');
                continue;
                 */
            }

            if ($this->label && $this->inline) {
                $el->placeholder = $label;
                $label = $this->label;
                $this->label = null;
            } elseif ($this->label || $this->inline) {
                $template = $field_no_label;
                $el->placeholder = $label;
            }

            // Fields get extra pampering
            $template->setHTML('Input', $el->getHTML());
            $template->trySet('label', $label);
            $template->trySet('label_for', $el->id.'_input');
            $template->set('field_class', $el->getFieldClass());

            if ($el->field->required) {
                $template->append('field_class', 'required ');
            }

            if (isset($el->width)) {
                $template->append('field_class', $el->width.' wide ');
            }

            if ($el->hint && $template->hasTag('Hint')) {
                $hint = new \atk4\ui\Label([null, 'pointing', 'id'=>$el->id.'_hint']);
                if (is_object($el->hint) || is_array($el->hint)) {
                    $hint->add($el->hint);
                } else {
                    $hint->set($el->hint);
                }
                $template->setHTML('Hint', $hint->getHTML());
            } elseif ($template->hasTag('Hint')) {
                $template->del('Hint');
            }

            $this->template->appendHTML('Content', $template->render());
        }

        // Now collect JS from everywhere
        foreach ($this->elements as $el) {
            if ($el->_js_actions) {
                $this->_js_actions = array_merge_recursive($this->_js_actions, $el->_js_actions);
            }
        }
    }
}
