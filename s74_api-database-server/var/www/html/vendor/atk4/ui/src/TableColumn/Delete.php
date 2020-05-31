<?php

namespace atk4\ui\TableColumn;

/**
 * Formatting action buttons column.
 */
class Delete extends Generic
{
    public function init()
    {
        parent::init();

        $this->vp = $this->table->_add(new \atk4\ui\CallbackLater());
        $this->vp->set(function () {
            $this->table->model->load($_POST[$this->name])->delete();

            $reload = $this->table->reload ?: $this->table;

            $this->table->app->terminate($reload->renderJSON());
        });
    }

    public function getDataCellTemplate(\atk4\data\Field $f = null)
    {
        $this->table->on('click', 'a.'.$this->short_name)->atkAjaxec([
            'uri'         => $this->vp->getJSURL(),
            'uri_options' => [$this->name => $this->table->jsRow()->data('id')],
            'confirm'     => (new \atk4\ui\jQuery())->attr('title'),
        ]);

        return $this->app->getTag(
            'a',
            ['href' => '#', 'title' => 'Delete {$'.$this->table->model->title_field.'}?', 'class' => $this->short_name],
            [
                ['i', ['class' => 'ui red trash icon'], ''],
                'Delete',
            ]);
    }
}
