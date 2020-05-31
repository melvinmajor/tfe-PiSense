<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Implements a more sophisticated and interractive Data-Table component.
 */
class CRUD extends Grid
{
    /** @var array of fields to show */
    public $fieldsDefault = null;

    /** @var array of fields to show in grid */
    public $fieldsRead = null;

    /** @var array of fields to show on form when adding new record */
    public $fieldsCreate = null;

    /** @var array of fields to show on form when editing a record */
    public $fieldsUpdate = null;

    /** @var array|Form Seed for form that is used by default * */
    public $formDefault = ['Form', 'layout' => 'Columns'];

    /** @var array|Form Seed for form that is used when adding * */
    public $formCreate = null;

    /** @var array|Form Seed for form that is used when editing * */
    public $formUpdate = null;

    /** @var array|View Seed for VirtualPage to use in modal * */
    public $pageDefault = ['VirtualPage'];

    /** @var array|View Seed for virtual page when adding * */
    public $pageCreate = null;

    /** @var array|View Seed for virtual page when editing * */
    public $pageUpdate = null;

    /** @var Item Will be set to menu item for new record add action * */
    public $itemCreate = null;

    /** @var array Default action to perform when adding or editing is successful * */
    public $notifyDefault = ['jsNotify', 'content' => 'Data is saved!', 'color'   => 'green'];

    /** @var array Action to perform when adding is successful * */
    public $notifyCreate = null;

    /** @var array Action to perform when editing is successful * */
    public $notifyUpdate = null;

    /**
     * Permitted operations. You can add more of your own.
     *
     * @var bool if 'true' user can create records
     */
    public $canCreate = true;

    /** @var bool If 'true' then user can update records * */
    public $canUpdate = true;

    /** @var bool If 'true' user can delete records * */
    public $canDelete = true;

    public function init()
    {
        parent::init();

        if ($this->paginator) {
            $this->stickyGet($this->paginator->name);
        }
        $this->stickyGet('_q');

        if ($this->canUpdate) {
            $this->pageUpdate = $this->add($this->pageUpdate ?: $this->pageDefault, ['short_name'=>'edit']);
        }

        if ($this->canCreate) {
            $this->pageCreate = $this->add($this->pageCreate ?: $this->pageDefault, ['short_name'=>'add']);
        }
    }

    /**
     * @deprecated 1.4 use $canCreate, $canDelete etc
     *
     * @param mixed $operation
     */
    public function can($operation)
    {
        throw new Exception('Please simply check $crud->canCreate or similar property directly');
    }

    /**
     * Sets data model of CRUD.
     *
     * @param \atk4\data\Model $m
     * @param array            $defaultFields
     *
     * @return \atk4\data\Model
     */
    public function setModel(\atk4\data\Model $m, $defaultFields = null)
    {
        if ($defaultFields !== null) {
            $this->fieldsDefault = $defaultFields;
        }

        parent::setModel($m, $this->fieldsRead ?: $this->fieldsDefault);
        $this->model->unload();

        if ($this->canCreate) {
            $this->initCreate();
        }

        if ($this->canUpdate) {
            $this->initUpdate();
        }

        if ($this->canDelete) {
            $this->initDelete();
        }

        return $this->model;
    }

    /**
     * Initializes interface elements for new record creation.
     *
     * @return array|jsExpression
     */
    public function initCreate()
    {
        // setting itemCreate manually is possible.
        if (!$this->itemCreate) {
            if (!$this->menu) {
                throw new Exception('Can not add create button without menu');
            }
            $this->itemCreate = $this->menu->addItem(['Add new '.$this->model->getModelCaption(), 'icon' => 'plus']);
        }
        $this->itemCreate->on('click.atk_CRUD', new jsModal('Add new', $this->pageCreate, [$this->name.'_sort' => $this->getSortBy()]));

        // setting callback for the page
        $this->pageCreate->set(function () {

            // formCreate may already be an object added inside pageCreate
            if (!is_object($this->formCreate) || !$this->formCreate->_initialized) {
                $this->formCreate = $this->pageCreate->add($this->formCreate ?: $this->formDefault);
            }

            // $form = $page->add($this->formCreate ?: ['Form', 'layout' => 'FormLayout/Columns']);
            $this->formCreate->setModel($this->model, $this->fieldsCreate ?: $this->fieldsDefault);

            if ($sortBy = $this->getSortBy()) {
                $this->formCreate->stickyGet($this->name.'_sort', $sortBy);
            }

            // set save handler with reload trigger
            // adds default submit hook if it is not already set for this form
            if (!$this->formCreate->hookHasCallbacks('submit')) {
                $this->formCreate->onSubmit(function ($form) {
                    $form->model->save();

                    return $this->jsSaveCreate();
                });
            }
        });
    }

    /**
     * Apply ordering to the current model as per the sort parameters.
     */
    public function applySort()
    {
        parent::applySort();

        if ($this->getSortBy() && $this->itemCreate) {
            //Remove previous click handler to Add new Item button and attach new one using sort argument.
            $this->container->js(true, $this->itemCreate->js()->off('click.atk_CRUD'));
            $this->container->js(true,
                                 $this->itemCreate->js()->on('click.atk_CRUD',
                                 new jsFunction([
                                     new jsModal('Add new', $this->pageCreate, [$this->name.'_sort' => $this->getSortBy()]),
                                 ]))
            );
        }
    }

    /**
     * Method to override to create a custom JavaScript action to execute
     * when submitting create form.
     *
     * @return array|jsExpression
     */
    public function jsSaveCreate()
    {
        return $this->jsSave($this->notifyCreate ?: $this->notifyDefault);
    }

    /**
     * Initializes interface elements for editing records.
     *
     * @return array|jsExpression
     */
    public function initUpdate()
    {
        $this->addAction(['icon' => 'edit'], new jsModal('Edit', $this->pageUpdate, [$this->name => $this->jsRow()->data('id'), $this->name.'_sort' => $this->getSortBy()]));

        $this->pageUpdate->set(function () {
            $this->model->load($this->app->stickyGet($this->name));

            // Maybe developer has already created form
            if (!is_object($this->formUpdate) || !$this->formUpdate->_initialized) {
                $this->formUpdate = $this->pageUpdate->add($this->formUpdate ?: $this->formDefault);
            }

            $this->formUpdate->setModel($this->model, $this->fieldsUpdate ?: $this->fieldsDefault);

            if ($sortBy = $this->getSortBy()) {
                $this->formUpdate->stickyGet($this->name.'_sort', $sortBy);
            }

            // set save handler with reload trigger
            // adds default submit hook if it is not already set for this form
            if (!$this->formUpdate->hookHasCallbacks('submit')) {
                $this->formUpdate->onSubmit(function ($form) {
                    $form->model->save();

                    return $this->jsSaveUpdate();
                });
            }
        });
    }

    /**
     * Method to override to create a custom JavaScript action to execute
     * when submitting update form.
     *
     * @return array|jsExpression
     */
    public function jsSaveUpdate()
    {
        return $this->jsSave($this->notifyUpdate ?: $this->notifyDefault);
    }

    /**
     * Default js action when saving form.
     *
     * @throws \atk4\core\Exception
     *
     * @return array
     */
    public function jsSave($notifier)
    {
        return [
            // close modal
            new jsExpression('$(".atk-dialog-content").trigger("close")'),

            // display notification
            $this->factory($notifier),

            // reload Grid Container.
            $this->container->jsReload([$this->name.'_sort' => $this->getSortBy()]),
        ];
    }

    /**
     * Initialize UI for deleting records.
     */
    public function initDelete()
    {
        $this->addAction(['icon' => 'red trash'], function ($jschain, $id) {
            $this->model->load($id)->delete();

            return $jschain->closest('tr')->transition('fade left');
        }, 'Are you sure?');
    }
}
