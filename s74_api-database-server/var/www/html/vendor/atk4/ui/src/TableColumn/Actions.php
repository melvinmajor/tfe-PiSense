<?php

namespace atk4\ui\TableColumn;

/**
 * Formatting action buttons column.
 */
class Actions extends Generic
{
    public $actions = [];

    public function init()
    {
        parent::init();
        $this->addClass('right aligned');
    }

    /**
     * Adds a new button which will execute $callback when clicked.
     *
     * Returns button object
     */
    public function addAction($button, $callback, $confirm = false)
    {
        $name = $this->name.'_action_'.(count($this->actions) + 1);

        if (!is_object($button)) {
            $button = new \atk4\ui\Button($button);
        }
        $button->app = $this->table->app;

        $this->actions[$name] = $button;
        $button->addClass('b_'.$name);
        $button->addClass('compact');
        $this->table->on('click', '.b_'.$name, $callback, [$this->table->jsRow()->data('id'), 'confirm' => $confirm]);

        return $button;
    }

    /**
     * Adds a new button which will open a modal dialog and dynamically
     * load contents through $callback. Will pass a virtual page.
     */
    public function addModal($button, $title, $callback, $owner = null)
    {
        if (!$owner) {
            $modal = $this->owner->owner->add(['Modal', 'title'=>$title]);
        } else {
            $modal = $owner->add(['Modal', 'title'=>$title]);
        }
        $modal->observeChanges(); // adds scrollbar if needed

        $modal->set(function ($t) use ($callback) {
            call_user_func($callback, $t, $this->app->stickyGet($this->name));
        });

        return $this->addAction($button, $modal->show([$this->name=>$this->owner->jsRow()->data('id')]));
    }

    /**
     * {@inheritdoc}
     */
    public function getTag($position, $value, $attr = [])
    {
        if ($this->table->hasCollapsingCssActionColumn && $position === 'body') {
            $attr['class'][] = 'collapsing';
        }

        return parent::getTag($position, $value, $attr);
    }

    public function getDataCellTemplate(\atk4\data\Field $f = null)
    {
        if (!$this->actions) {
            return '';
        }

        // render our actions
        $output = '';
        foreach ($this->actions as $action) {
            $output .= $action->getHTML();
        }

        return '<div class="ui buttons">'.$output.'</div>';
    }

    // rest will be implemented for crud
}
