<?php

// vim:ts=4:sw=4:et:fdm=marker:fdl=0

namespace atk4\ui;

/**
 * Implements a more sophisticated and interactive Data-Table component.
 */
class Grid extends View
{
    /**
     * Will be initialized to Menu object, however you can set this to false to disable menu.
     *
     * @var Menu|false
     */
    public $menu = null;

    /**
     * Calling addQuickSearch will create a form with a field inside $menu to perform quick searches.
     *
     * If you pass this property as array of field names while creating Grid, then when you will call
     * setModel() QuickSearch object will be created automatically and these model fields will be used
     * for filtering.
     *
     * @var array|FormField\Generic
     */
    public $quickSearch = null;

    /**
     * Paginator is automatically added below the table and will divide long tables into pages.
     *
     * You can provide your own Paginator object here to customize.
     *
     * @var Paginator|false
     */
    public $paginator = null;

    /**
     * Number of items per page to display.
     *
     * @var int
     */
    public $ipp = 50;

    /**
     * Calling addAction will add a new column inside $table, and will be re-used
     * for next addAction().
     *
     * @var TableColumn\Actions
     */
    public $actions = null;

    /**
     * Calling addSelection will add a new column inside $table, containing checkboxes.
     * This column will be stored here, in case you want to access it.
     *
     * @var TableColumn\CheckBox
     */
    public $selection = null;

    /**
     * Grid can be sorted by clicking on column headers. This will be automatically enabled
     * if Model supports ordering. You may override by setting true/false.
     *
     * @var bool
     */
    public $sortable = null;

    /**
     * Set this if you want GET argument name to look beautifully for sorting.
     *
     * @var null|string
     */
    public $sortTrigger = null;

    /**
     * Component that actually renders data rows / columns and possibly totals.
     *
     * @var Table|false
     */
    public $table = null;

    /**
     * The container for table and paginator.
     *
     * @var View
     */
    public $container = null;

    public $defaultTemplate = 'grid.html';

    // Defines which Table Decorator to use for Actions
    protected $actionDecorator = 'Actions';

    public function init()
    {
        parent::init();
        $this->container = $this->add(['View', 'template' => $this->template->cloneRegion('Container')]);
        $this->template->del('Container');

        if (!$this->sortTrigger) {
            $this->sortTrigger = $this->name.'_sort';
        }

        if ($this->menu !== false) {
            $this->menu = $this->add($this->factory(['Menu', 'activate_on_click' => false], $this->menu), 'Menu');
        }

        $this->table = $this->container->add($this->factory(['Table', 'very compact very basic striped single line', 'reload' => $this->container], $this->table), 'Table');

        if ($this->paginator !== false) {
            $seg = $this->container->add(['View'], 'Paginator')->addStyle('text-align', 'center');
            $this->paginator = $seg->add($this->factory(['Paginator', 'reload' => $this->container], $this->paginator));
        }
    }

    /**
     * Add new column to grid. If column with this name already exists,
     * an. Simply calls Table::addColumn(), so check that method out.
     *
     * @param string                   $name            Data model field name
     * @param array|string|object|null $columnDecorator
     * @param array|string|object|null $field
     *
     * @return TableColumn\Generic
     */
    public function addColumn($name, $columnDecorator = null, $field = null)
    {
        return $this->table->addColumn($name, $columnDecorator, $field);
    }

    /**
     * Add additional decorator for existing column.
     *
     * @param string                    $name      Column name
     * @param TableColumn\Generic|array $decorator Seed or object of the decorator
     */
    public function addDecorator($name, $decorator)
    {
        return $this->table->addDecorator($name, $decorator);
    }

    /**
     * Add a new buton to the Grid Menu with a given text.
     *
     * WARNING: needs to be reviewed!
     *
     * @param mixed $text
     */
    public function addButton($text)
    {
        if (!$this->menu) {
            throw new Exception(['Unable to add Button without Menu']);
        }

        return $this->menu->addItem()->add(new Button($text));
    }

    /**
     * Set item per page value.
     *
     * if an array is passed, it will also add an ItemPerPageSelector to paginator.
     *
     * @param int|array $ipp
     * @param string    $label
     *
     * @throws Exception
     */
    public function setIpp($ipp, $label = 'Item per pages:')
    {
        if (is_array($ipp)) {
            $this->addItemsPerPageSelector($ipp, $label);
            if (@$_GET['ipp']) {
                $this->ipp = $_GET['ipp'];
            } else {
                $this->ipp = $ipp[0];
            }
        } else {
            $this->ipp = $ipp;
        }
    }

    /**
     * Add ItemsPerPageSelector View in grid menu or paginator in order to dynamically setup number of item per page.
     *
     * @param array  $items An array of item's per page value.
     * @param string $label The memu item label.
     *
     * @throws Exception
     *
     * @return $this
     */
    public function addItemsPerPageSelector($items = [10, 25, 50, 100], $label = 'Item per pages:')
    {
        if ($ipp = $this->container->stickyGet('ipp')) {
            $this->ipp = $ipp;
        } else {
            $this->ipp = $items[0];
        }

        $pageLength = $this->paginator->add(['ItemsPerPageSelector', 'pageLengthItems' => $items, 'label' => $label, 'currentIpp' => $this->ipp], 'afterPaginator');
        $this->paginator->template->trySet('PaginatorType', 'ui grid');

        if ($sortBy = $this->getSortBy()) {
            $pageLength->stickyGet($this->sortTrigger, $sortBy);
        }

        $pageLength->onPageLengthSelect(function ($ipp) use ($pageLength) {
            $this->ipp = $ipp;
            $this->setModelLimitFromPaginator();
            //add ipp to quicksearch
            if ($this->quickSearch instanceof jsSearch) {
                $this->container->js(true, $this->quickSearch->js()->atkJsSearch('setUrlArgs', ['ipp', $this->ipp]));
            }
            $this->applySort();

            //return the view to reload.
            return $this->container;
        });

        return $this;
    }

    /**
     * Add dynamic scrolling paginator.
     *
     * @param int    $ipp          Number of item per page to start with.
     * @param array  $options      An array with js Scroll plugin options.
     * @param View   $container    The container holding the lister for scrolling purpose. Default to view owner.
     * @param string $scrollRegion A specific template region to render. Render output is append to container html element.
     *
     * @throws Exception
     *
     * @return $this
     */
    public function addJsPaginator($ipp, $options = [], $container = null, $scrollRegion = 'Body')
    {
        if ($this->paginator) {
            $this->paginator->destroy();
            //prevent action(count) to be output twice.
            $this->paginator = null;
        }

        if ($sortBy = $this->getSortBy()) {
            $this->stickyGet($this->sortTrigger, $sortBy);
        }
        $this->applySort();

        $this->table->addJsPaginator($ipp, $options, $container, $scrollRegion);

        return $this;
    }

    /**
     * Add dynamic scrolling paginator in container.
     * Use this to make table headers fixed.
     *
     * @param int    $ipp             Number of item per page to start with.
     * @param int    $containerHeight Number of pixel the table container should be.
     * @param array  $options         An array with js Scroll plugin options.
     * @param View   $container       The container holding the lister for scrolling purpose. Default to view owner.
     * @param string $scrollRegion    A specific template region to render. Render output is append to container html element.
     *
     * @throws Exception
     *
     * @return $this
     */
    public function addJsPaginatorInContainer($ipp, $containerHeight, $options = [], $container = null, $scrollRegion = 'Body')
    {
        $this->table->hasCollapsingCssActionColumn = false;
        $options = array_merge($options, [
          'hasFixTableHeader'    => true,
          'tableContainerHeight' => $containerHeight,
        ]);
        //adding a state context to js scroll plugin.
        $options = array_merge(['stateContext' => '#'.$this->container->name], $options);

        return $this->addJsPaginator($ipp, $options, $container, $scrollRegion);
    }

    /**
     * Add Search input field using js action.
     * By default, will query server when using Enter key on input search field.
     * You can change it to query server on each keystroke by passing $autoQuery true,.
     *
     * @param array $fields       The list of fields to search for.
     * @param bool  $hasAutoQuery Will query server on each key pressed.
     *
     * @throws Exception
     * @throws \atk4\data\Exception
     */
    public function addQuickSearch($fields = [], $hasAutoQuery = false)
    {
        if (!$this->model) {
            throw new Exception(['Call setModel() before addQuickSearch()']);
        }

        if (!$fields) {
            $fields = [$this->model->title_field];
        }

        if (!$this->menu) {
            throw new Exception(['Unable to add QuickSearch without Menu']);
        }

        $view = $this->menu
            ->addMenuRight()->addItem()->setElement('div')
            ->add('View');

        $this->quickSearch = $view->add(['jsSearch', 'reload' => $this->container, 'autoQuery' => $hasAutoQuery]);

        if ($q = $this->stickyGet('_q')) {
            $cond = [];
            foreach ($fields as $field) {
                $cond[] = [$field, 'like', '%'.$q.'%'];
            }
            $this->model->addCondition($cond);
        }
    }

    /**
     * Returns JS for reloading View.
     *
     * @param array $args
     *
     * @return \atk4\ui\jsReload
     */
    public function jsReload($args = [])
    {
        return new jsReload($this->container, $args);
    }

    /**
     * Adds a new button into the action column on the right. For CRUD this
     * column will already contain "delete" and "edit" buttons.
     *
     * @param string|array|View         $button  Label text, object or seed for the Button
     * @param jsExpressionable|callable $action  JavaScript action or callback
     * @param bool|string               $confirm Should we display confirmation "Are you sure?"
     */
    public function addAction($button, $action, $confirm = false)
    {
        if (!$this->actions) {
            $this->actions = $this->table->addColumn(null, $this->actionDecorator);
        }

        return $this->actions->addAction($button, $action, $confirm);
    }

    /**
     * An array of column name where filter is needed.
     * Leave empty to include all column in grid.
     *
     * @param array|null $names An array with the name of column.
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     *
     * @return $this
     */
    public function addFilterColumn($names = null)
    {
        if (!$this->menu) {
            throw new Exception(['Unable to add Filter Column without Menu']);
        }
        $this->menu->addItem(['Clear Filters'], new \atk4\ui\jsReload($this->table->reload, ['atk_clear_filter' => 1]));
        $this->table->setFilterColumn($names);

        return $this;
    }

    /**
     * Add a dropdown menu to header column.
     *
     * @param string   $columnName The name of column where to add dropdown.
     * @param array    $items      The menu items to add.
     * @param callable $fx         The callback function to execute when an item is selected.
     * @param string   $icon       The icon.
     * @param string   $menuId     The menu id return by callback.
     *
     * @throws Exception
     */
    public function addDropdown($columnName, $items, $fx, $icon = 'caret square down', $menuId = null)
    {
        $column = $this->table->columns[$columnName];
        if (!isset($column)) {
            throw new Exception('The column where you want to add dropdown does not exist: '.$columnName);
        }
        if (!$menuId) {
            $menuId = $columnName;
        }

        $column->addDropdown($items, function ($item) use ($fx) {
            return call_user_func($fx, [$item]);
        }, $icon, $menuId);
    }

    /**
     * Add a popup to header column.
     *
     * @param string $columnName The name of column where to add popup.
     * @param Popup  $popup      Popup view.
     * @param string $icon       The icon.
     *
     * @throws Exception
     *
     * @return mixed
     */
    public function addPopup($columnName, $popup = null, $icon = 'caret square down')
    {
        $column = $this->table->columns[$columnName];
        if (!isset($column)) {
            throw new Exception('The column where you want to add popup does not exist: '.$columnName);
        }

        return $column->addPopup($popup, $icon);
    }

    /**
     * Similar to addAction but when button is clicked, modal is displayed
     * with the $title and $callback is executed through VirtualPage.
     *
     * @param string|array|View $button
     * @param string            $title
     * @param callable          $callback function($page){ . .}
     */
    public function addModalAction($button, $title, $callback)
    {
        if (!$this->actions) {
            $this->actions = $this->table->addColumn(null, 'Actions');
        }

        return $this->actions->addModal($button, $title, $callback, $this);
    }

    /**
     * Get sortBy value from url parameter.
     *
     * @return null|string
     */
    public function getSortBy()
    {
        return isset($_GET[$this->sortTrigger]) ? $_GET[$this->sortTrigger] : null;
    }

    /**
     * Apply ordering to the current model as per the sort parameters.
     */
    public function applySort()
    {
        if ($this->sortable === false) {
            return;
        }

        $sortBy = $this->getSortBy();

        if ($sortBy && $this->paginator) {
            $this->paginator->addReloadArgs([$this->sortTrigger => $sortBy]);
        }

        $desc = false;
        if ($sortBy && $sortBy[0] == '-') {
            $desc = true;
            $sortBy = substr($sortBy, 1);
        }

        $this->table->sortable = true;

        if (
            $sortBy
            && isset($this->table->columns[$sortBy])
            && $this->model->hasElement($sortBy) instanceof \atk4\data\Field
        ) {
            $this->model->setOrder($sortBy, $desc);
            $this->table->sort_by = $sortBy;
            $this->table->sort_order = $desc ? 'descending' : 'ascending';
        }

        $this->table->on(
            'click',
            'thead>tr>th',
            new jsReload($this->container, [$this->sortTrigger => (new jQuery())->data('column')])
        );
    }

    /**
     * Sets data Model of Grid.
     *
     * If $columns is not defined, then automatically will add columns for all
     * visible model fields. If $columns is set to false, then will not add
     * columns at all.
     *
     * @param \atk4\data\Model $m       Data model
     * @param array|bool       $columns
     *
     * @return \atk4\data\Model
     */
    public function setModel(\atk4\data\Model $model, $columns = null)
    {
        $this->model = $this->table->setModel($model, $columns);

        if ($this->quickSearch && is_array($this->quickSearch)) {
            $this->addQuickSearch($this->quickSearch);
        }

        return $this->model;
    }

    /**
     * Makes rows of this grid selectable by creating new column on the left with
     * checkboxes.
     *
     * @return TableColumn\CheckBox
     */
    public function addSelection()
    {
        $this->selection = $this->table->addColumn(null, 'CheckBox');

        // Move element to the beginning
        $k = array_search($this->selection, $this->table->columns);
        $this->table->columns = [$k => $this->table->columns[$k]] + $this->table->columns;

        return $this->selection;
    }

    /**
     * Add column with drag handler on each row.
     * Drag handler allow to reorder table via drag n drop.
     *
     * @return TableColumn\Generic
     */
    public function addDragHandler()
    {
        $handler = $this->table->addColumn(null, 'DragHandler');
        // Move last column to the beginning in table column array.
        array_unshift($this->table->columns, array_pop($this->table->columns));

        return $handler;
    }

    /**
     * Will set model limit according to paginator value.
     *
     * @throws \atk4\data\Exception
     * @throws \atk4\dsql\Exception
     */
    private function setModelLimitFromPaginator()
    {
        $this->paginator->setTotal(ceil($this->model->action('count')->getOne() / $this->ipp));
        $this->model->setLimit($this->ipp, ($this->paginator->page - 1) * $this->ipp);
    }

    /**
     * Renders view.
     *
     * Before rendering take care of data sorting.
     */
    public function renderView()
    {
        // take care of sorting
        $this->applySort();

        return parent::renderView();
    }

    /**
     * Recursively renders view.
     */
    public function recursiveRender()
    {
        // bind with paginator
        if ($this->paginator) {
            $this->setModelLimitFromPaginator();
        }

        if ($this->quickSearch instanceof jsSearch) {
            if ($sortBy = $this->getSortBy()) {
                $this->container->js(true, $this->quickSearch->js()->atkJsSearch('setUrlArgs', [$this->sortTrigger, $sortBy]));
            }
        }

        return parent::recursiveRender();
    }

    /**
     * Proxy function for Table::jsRow().
     *
     * @return jQuery
     */
    public function jsRow()
    {
        return $this->table->jsRow();
    }
}
