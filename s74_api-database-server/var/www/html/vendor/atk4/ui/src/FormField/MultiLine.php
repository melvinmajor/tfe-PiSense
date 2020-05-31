<?php
/**
 * Create a multiple line input field.
 * Allow to add/edit multiple row of a data table.
 * If model define in Multiline contains expression, these expression
 * will be evaluate on the fly while entering data.
 * ex: Invoice Items model
 *     $model->addExpression('total', ['expr'=>'[qty]*[price]', 'type' => 'money', 'caption' => 'Total']);
 * If using total field in row of record, then the expression will be evaluated when user enter data in each
 * row.
 *
 * Each rows will be added to db using Multiline::saveRows() method,
 * usually on form submit. If you have setup a form model where multiline's
 * model is a reference to your form model, form model should be saved prior to call saveRows() method.
 *
 *  $f = $app->add('Form');
 *  $f->setModel($invoice, false);
 *  //setup fiel in layout..
 *
 *  //add multiline field and set model for it.
 *  $ml = $f->addField('ml', ['Multiline']);
 *
 *  //setting model using hasMany reference of Invoice.
 *  $ml->setModel($invoice, ['item','cat','qty','price', 'total'], 'Items', 'invoice_id');
 *
 *  $f->onSubmit(function($f) use ($ml) {
 *      $f->model->save();
 *      $ml->saveRows();
 *      return new \atk4\ui\jsToast('Saved!');
 *  });
 *
 *  Multiline input also has an onChange callback that will return all rows data in an array.
 *  It is also possible to fire onChange handler only for certain fields by passing
 *  them as an array to the method.
 *  Note that deleting a row will always fire the onChange callback.
 *
 *  Use this return data to update other related area of the form.
 *  Ex: Updating Grand Total field of all invoice item.
 *
 *  $ml->onChange(function($rows) use ($f) {
 *      $grand_total = 0;
 *      foreach ($rows as $row => $cols) {
 *          foreach ($cols as $col) {
 *              $fieldName = key($col);
 *                  if ($fieldName === 'total') {
 *                      $grand_total = $grand_total + $col[$fieldName];
 *                  }
 *           }
 *      }
 *    return $f->js(true, null, 'input[name="grand_total"]')->val(number_format($grand_total, 2));
 *  }, ['qty', 'price']);
 *
 * Finally, it is also possible to use Multiline for quickly adding record to a model.
 * Be aware that all record in User model will be used. So if your model contains a lot
 * of record, you should control it's limit somehow.
 *
 * $f = $app->add('Form');
 * $ml = $f->addField('ml', ['MultiLine']);
 * $ml->setModel($user, ['name','is_vip']);
 *
 * $f->onSubmit(function($f) use ($ml) {
 *     $ml->saveRows();
 *     return new \atk4\ui\jsToast('Saved!');
 * });
 *
 * 2019-05-06   - now check if field isEditable instead of just expression when saving row(line 376).
 *              - Add options property for table css options.
 * 2019-05-07   - add form as parameter to the onChange callback. This allow to perform calculation at form model level.
 */

namespace atk4\ui\FormField;

use atk4\data\Field\Callback;
use atk4\data\Field_SQL_Expression;
use atk4\data\Model;
use atk4\data\ValidationException;
use atk4\ui\Exception;
use atk4\ui\jsVueService;
use atk4\ui\Template;

class MultiLine extends Generic
{
    /**
     * Layout view as is within form layout.
     *
     * @var bool
     */
    public $layoutWrap = false;

    /**
     * The template need for the multiline view.
     *
     * @var null
     */
    public $multiLineTemplate = null;

    /**
     * The multiline View.
     * Assign on init.
     *
     * @var null
     */
    private $multiLine = null;

    /**
     * An array of options for sui-table property.
     * example: ['celled' => true] will render column line in table.
     *
     * @var array
     */
    public $options = [];

    /**
     * The definition of each fields used in each multiline row.
     *
     * @var null
     */
    private $fieldDefs = null;

    /**
     * The js callback.
     *
     * @var null
     */
    private $cb = null;

    /**
     * The callback function trigger when field
     * are changed or row are delete.
     *
     * @var null
     */
    public $changeCb = null;

    /**
     * An array of fields name that will trigger
     * the change callback when field are changed.
     *
     * @var null
     */
    public $eventFields = null;

    /**
     * Collection of field errors.
     *
     * @var null
     */
    private $rowErrors = null;

    /**
     * The model reference use for multi line input.
     *
     * @var null
     */
    public $modelRef = null;

    /**
     * The link field used for reference.
     *
     * @var null
     */
    public $linkField = null;

    /**
     * The fields use in each line.
     *
     * @var null
     */
    public $rowFields = null;

    /**
     * The data sent for each line.
     *
     * @var null
     */
    public $rowData = null;

    public function init()
    {
        parent::init();

        $this->app->useSuiVue();

        if (!$this->multiLineTemplate) {
            $this->multiLineTemplate = new Template('<div id="{$_id}" class="ui"><atk-multiline v-bind="initData"></atk-multiline>{$Input}</div>');
        }

        $this->multiLine = $this->add(['View', 'template' => $this->multiLineTemplate]);

        $this->cb = $this->add('jsCallback');

        //load data associate with this input and validate it.
        $this->form->addHook('loadPOST', function ($form) {
            $this->rowData = json_decode($_POST[$this->short_name], true);
            if ($this->rowData) {
                $this->rowErrors = $this->validate($this->rowData);
                if ($this->rowErrors) {
                    throw new ValidationException([$this->short_name => 'multine error']);
                }
            }
        });

        // Change form error handling.
        $this->form->addHook('displayError', function ($form, $fieldName, $str) {
            // When error are coming from this multiline field then advice multiline component about these errors.
            // otherwise use normal field error.
            if ($fieldName === $this->short_name) {
                $jsError = [(new jsVueService())->emitEvent('atkml-row-error', ['id' => $this->multiLine->name, 'errors' => $this->rowErrors])];
            } else {
                $jsError = [$form->js()->form('add prompt', $fieldName, $str)];
            }

            return $jsError;
        });
    }

    /**
     * Add a callback when fields are changed.
     * You must supply array of fields that will trigger the
     * callback when changed.
     *
     * @param array|\atk4\ui\FormField\jsExpression|callable|string $fx
     * @param arra                                                  $fields
     *
     * @throws Exception
     */
    public function onLineChange($fx, $fields)
    {
        if (!is_callable($fx)) {
            throw new Exception('Function is required for onChange event.');
        }
        $this->eventFields = $fields;

        $this->changeCb = $fx;
    }

    /**
     * Input field collecting multiple rows of data.
     *
     * @throws \atk4\core\Exception
     *
     * @return string
     */
    public function getInput()
    {
        return $this->app->getTag('input', [
            'name'        => $this->short_name,
            'type'        => 'hidden',
            'value'       => $this->getValue(),
            'readonly'    => true,
        ]);
    }

    /**
     * Get multiline initial field value.
     * Value is based on model set and will
     * output data rows as json string value.
     *
     *
     * @throws \atk4\core\Exception
     *
     * @return false|string
     */
    public function getValue()
    {
        $m = null;
        $data = [];

        //set model according to model reference if set; or simply the model pass to it.
        if ($this->model->loaded() && $this->modelRef) {
            $m = $this->model->ref($this->modelRef);
        } elseif (!$this->modelRef) {
            $m = $this->model;
        }
        if ($m) {
            foreach ($m as $id => $row) {
                $d_row = [];
                foreach ($this->rowFields as $fieldName) {
                    $field = $m->getElement($fieldName);
                    if ($field->isEditable()) {
                        $value = $row->get($field);
                    } else {
                        $value = $this->app->ui_persistence->_typecastSaveField($field, $row->get($field));
                    }
                    $d_row[$fieldName] = $value;
                }
                $data[] = $d_row;
            }
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Validate each row and return errors if found.
     *
     * @param $rows
     *
     * @throws \atk4\core\Exception
     *
     * @return array|null
     */
    public function validate($rows)
    {
        $rowErrors = [];
        $m = $this->getModel();

        foreach ($rows as $row => $cols) {
            $rowId = $this->getMlRowId($cols);
            foreach ($cols as $col) {
                $fieldName = key($col);
                if ($fieldName === '__atkml' || $fieldName === $m->id_field) {
                    continue;
                }
                $value = $col[$fieldName];

                try {
                    $field = $m->getElement($fieldName);
                    // save field value only if field was editable in form at all
                    if (!$field->read_only) {
                        $m[$fieldName] = $this->app->ui_persistence->typecastLoadField($field, $value);
                    }
                } catch (\atk4\core\Exception $e) {
                    $rowErrors[$rowId][] = ['field' => $fieldName, 'msg' => $e->getMessage()];
                }
            }
            $rowErrors = $this->addModelValidateErrors($rowErrors, $rowId, $m);
        }

        if ($rowErrors) {
            return $rowErrors;
        }
    }

    /**
     * Save rows.
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     */
    public function saveRows()
    {
        // if we are using a reference, make sure main model is loaded.
        if ($this->modelRef && !$this->model->loaded()) {
            throw new Exception('Parent model need to be loaded');
        }

        $model = $this->model;
        if ($this->modelRef) {
            $model = $model->ref($this->modelRef);
        }

        $currentIds = [];
        foreach ($this->getModel() as $id => $data) {
            $currentIds[] = $id;
        }

        foreach ($this->rowData as $row => $cols) {
            $rowId = $this->getMlRowId($cols);
            if ($this->modelRef && $this->linkField) {
                $model[$this->linkField] = $this->model->get('id');
            }
            foreach ($cols as $col) {
                $fieldName = key($col);
                if ($fieldName === '__atkml') {
                    continue;
                }
                $value = $col[$fieldName];
                if ($fieldName === $model->id_field && $value) {
                    $model->load($value);
                }

                $field = $model->getElement($fieldName);

                if ($field->isEditable()) {
                    $field->set($value);
                }
            }
            $id = $model->save()->get($model->id_field); //->unload();
            $k = array_search($id, $currentIds);
            if ($k > -1) {
                unset($currentIds[$k]);
            }

            $model->unload();
        }
        // if currentId are still there, then delete them.
        foreach ($currentIds as $id) {
            $model->delete($id);
        }

        return $this;
    }

    /**
     * Check for model validate error.
     *
     * @param $errors
     * @param $rowId
     * @param $model
     *
     * @return mixed
     */
    protected function addModelValidateErrors($errors, $rowId, $model)
    {
        $e = $model->validate();
        if ($e) {
            foreach ($e as $f => $msg) {
                $errors[$rowId][] = ['field' => $f, 'msg' => $msg];
            }
        }

        return $errors;
    }

    /**
     * for javascript use - changing this method may brake js functionality.
     *
     * Return MultiLine row id in a row of data.
     *
     * @param $row
     *
     * @return |null
     */
    private function getMlRowId($row)
    {
        $rowId = null;
        foreach ($row as $k => $col) {
            foreach ($col as $fieldName => $value) {
                if ($fieldName === '__atkml') {
                    $rowId = $value;
                }
            }
            if ($rowId) {
                break;
            }
        }

        return $rowId;
    }

    /**
     * Will return a model reference if reference was set
     * in setModel, Otherwise, will return main model.
     *
     * @throws \atk4\core\Exception
     *
     * @return Model
     */
    public function getModel()
    {
        $m = $this->model;
        if ($this->modelRef) {
            $m = $m->ref($this->modelRef);
        }

        return $m;
    }

    /**
     * Set view model.
     * If modelRef is used then getModel will return proper model.
     *
     *
     * @param Model $m
     * @param array $fields
     * @param null  $modelRef
     * @param null  $linkField
     *
     * @throws Exception
     * @throws \atk4\core\Exception
     *
     * @return Model
     */
    public function setModel(\atk4\data\Model $model, $fields = [], $modelRef = null, $linkField = null)
    {
        //remove our self from model
        if ($model->hasElement($this->short_name)) {
            $model->getElement($this->short_name)->never_persist = true;
        }
        $m = parent::setModel($model);

        if ($modelRef) {
            if (!$linkField) {
                throw new Exception('Using model ref required to set $linkField');
            }
            $this->linkField = $linkField;
            $this->modelRef = $modelRef;
            $m = $m->ref($modelRef);
        }

        if (!$fields) {
            $fields = $this->getModelFields($m);
        }
        $this->rowFields = array_merge([$m->id_field], $fields);

        foreach ($this->rowFields as $fieldName) {
            $field = $m->getElement($fieldName);

            if (!$field instanceof \atk4\data\Field) {
                continue;
            }
            $type = $field->type ? $field->type : 'string';

            if (isset($field->ui['form'])) {
                $type = $field->ui['form'][0];
            }

            $this->fieldDefs[] = [
                'field'       => $field->short_name,
                'type'        => $type,
                'caption'     => $field->getCaption(),
                'default'     => $field->default,
                'isEditable'  => $field->isEditable(),
                'isHidden'    => $field->isHidden(),
                'isVisible'   => $field->isVisible(),
                'width'       => $field->ui['multiline']['width'] ? $field->ui['multiline']['width'] : null,
            ];
        }

        return $m;
    }

    /**
     * Returns array of names of fields to automatically include them in form.
     * This includes all editable or visible fields of the model.
     *
     * @param \atk4\data\Model $model
     *
     * @return array
     */
    protected function getModelFields(\atk4\data\Model $model)
    {
        $fields = [];
        foreach ($model->elements as $f) {
            if (!$f instanceof \atk4\data\Field) {
                continue;
            }

            if ($f->isEditable() || $f->isVisible()) {
                $fields[] = $f->short_name;
            }
        }

        return $fields;
    }

    public function renderView()
    {
        if (!$this->getModel()) {
            throw new Exception('Multiline field needs to have it\'s model setup.');
        }

        if ($this->cb->triggered()) {
            $this->cb->set(function () {
                try {
                    return $this->renderCallback();
                } catch (\atk4\Core\Exception $e) {
                    $this->app->terminate(json_encode(['success' => false, 'error' => $e->getMessage()]));
                } catch (\Error $e) {
                    $this->app->terminate(json_encode(['success' => false, 'error' => $e->getMessage()]));
                }
            });
        }

        $this->multiLine->template->setHTML('Input', $this->getInput());
        parent::renderView();

        $this->multiLine->vue('atk-multiline',
                              [
                                  'data' => [
                                      'linesField'  => $this->short_name,
                                      'fields'      => $this->fieldDefs,
                                      'idField'     => $this->getModel()->id_field,
                                      'url'         => $this->cb->getJSURL(),
                                      'eventFields' => $this->eventFields,
                                      'hasChangeCb' => $this->changeCb ? true : false,
                                      'options'     => $this->options,
                                  ],
                              ]);
    }

    /**
     * for javascript use - changing these methods may brake js functionality.
     *
     * Render callback.
     *
     * @throws ValidationException
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     */
    private function renderCallback()
    {
        $action = isset($_POST['__atkml_action']) ? $_POST['__atkml_action'] : null;
        $response = [
            'success' => true,
            'message' => 'Success',
        ];

        switch ($action) {
            case 'update-row':
                $m = $this->setDummyModelValue(clone $this->getModel());
                $expressionValues = array_merge($this->getExpressionValues($m), $this->getCallbackValues($m));
                $this->app->terminate(json_encode(array_merge($response, ['expressions' => $expressionValues])));
                break;
            case 'on-change':
                // let regular callback render output.
                return call_user_func_array($this->changeCb, [json_decode($_POST['rows'], true), $this->form]);
                break;
        }
    }

    /**
     * for javascript use - changing this method may brake js functionality.
     *
     * return values associate with callback field.
     *
     * @param $model
     *
     * @return array
     */
    private function getCallbackValues($model)
    {
        $values = [];
        foreach ($this->fieldDefs as $def) {
            $fieldName = $def['field'];
            if ($fieldName === $model->id_field) {
                continue;
            }
            $field = $model->getElement($fieldName);
            if ($field instanceof Callback) {
                $value = call_user_func($field->expr, $model);
                $values[$fieldName] = $this->app->ui_persistence->_typecastSaveField($field, $value);
            }
        }

        return $values;
    }

    /**
     * for javascript use - changing this method may brake js functionality.
     *
     * Looks inside the POST of the request and loads data into model.
     * Allow to Run expression base on rowData value.
     */
    private function setDummyModelValue($model)
    {
        $post = $_POST;

        foreach ($this->fieldDefs as $def) {
            $fieldName = $def['field'];
            if ($fieldName === $model->id_field) {
                continue;
            }

            $value = isset($post[$fieldName]) ? $post[$fieldName] : null;
            if ($model->getElement($fieldName)->isEditable()) {
                try {
                    $model[$fieldName] = $value;
                } catch (ValidationException $e) {
                    //bypass validation at this point.
                }
            }
        }

        return $model;
    }

    /**
     * for javascript use - changing this method may brake js functionality.
     *
     * Return values associated to field expression.
     *
     * @param $m
     *
     * @throws \atk4\core\Exception
     * @throws \atk4\data\Exception
     *
     * @return array
     */
    private function getExpressionValues($m)
    {
        $dummyFields = [];
        $formatValues = [];

        foreach ($this->getExpressionFields($m) as $k => $field) {
            $dummyFields[$k]['name'] = $field->short_name;
            $dummyFields[$k]['expr'] = $this->getDummyExpression($field, $m);
        }

        if (!empty($dummyFields)) {
            $dummyModel = new Model($m->persistence, ['table' => $m->table]);
            foreach ($dummyFields as $f) {
                $dummyModel->addExpression($f['name'], ['expr'=>$f['expr'], 'type' => $m->getElement($f['name'])->type]);
            }
            $values = $dummyModel->loadAny()->get();
            unset($values[$m->id_field]);

            foreach ($values as $f => $value) {
                $field = $m->getElement($f);
                $formatValues[$f] = $this->app->ui_persistence->_typecastSaveField($field, $value);
            }
        }

        return $formatValues;
    }

    /**
     * for javascript use - changing this method may brake js functionality.
     *
     * Get all field expression in model.
     * But only evaluate expression used in rowFields.
     *
     * @return array
     */
    private function getExpressionFields($model)
    {
        $fields = [];
        foreach ($model->elements as $f) {
            if (!$f instanceof Field_SQL_Expression || !in_array($f->short_name, $this->rowFields)) {
                continue;
            }

            $fields[] = $f;
        }

        return $fields;
    }

    /**
     * for javascript use - changing this method may brake js functionality.
     *
     * Return expression where field are replace with their current or default value.
     * ex: total field expression = [qty] * [price] will return 4 * 100
     * where qty and price current value are 4 and 100 respectively.
     *
     * @param $expr
     *
     * @throws \atk4\core\Exception
     *
     * @return mixed
     */
    private function getDummyExpression($exprField, $model)
    {
        $expr = $exprField->expr;
        $matches = [];

        preg_match_all('/\[[a-z0-9_]*\]|{[a-z0-9_]*}/i', $expr, $matches);

        foreach ($matches[0] as $match) {
            $fieldName = substr($match, 1, -1);
            $field = $model->getElement($fieldName);
            if ($field instanceof Field_SQL_Expression) {
                $expr = str_replace($match, $this->getDummyExpression($field, $model), $expr);
            } else {
                $expr = str_replace($match, $this->getValueForExpression($exprField, $fieldName, $model), $expr);
            }
        }

        return $expr;
    }

    /**
     * for javascript use - changing this method may brake js functionality.
     *
     * Return a value according to field use in expression and the expression type.
     * If field use in expression is null , the default value is return.
     *
     * @param $exprField
     * @param $fieldName
     *
     * @return int|mixed|string
     */
    private function getValueForExpression($exprField, $fieldName, $model)
    {
        switch ($exprField->type) {
            case 'money':
            case 'integer':
            case 'number':
                //Value is 0 or the field value.
                $value = $model[$fieldName] ? $model[$fieldName] : 0;
                break;
            default:
                //Value is "" or field value enclosed in bracket: "value"
                $value = $model[$fieldName] ? '"'.$model[$fieldName].'"' : '""';
        }

        return $value;
    }
}
