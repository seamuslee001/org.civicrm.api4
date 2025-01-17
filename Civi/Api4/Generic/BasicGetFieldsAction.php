<?php

namespace Civi\Api4\Generic;

use Civi\API\Exception\NotImplementedException;
use Civi\Api4\Utils\ActionUtil;

/**
 * Get fields for an entity.
 *
 * @method $this setLoadOptions(bool $value)
 * @method bool getLoadOptions()
 * @method $this setAction(string $value)
 */
class BasicGetFieldsAction extends BasicGetAction {

  /**
   * Fetch option lists for fields?
   *
   * @var bool
   */
  protected $loadOptions = FALSE;

  /**
   * @var string
   */
  protected $action = 'get';

  /**
   * To implement getFields for your own entity:
   *
   * 1. From your entity class add a static getFields method.
   * 2. That method should construct and return this class.
   * 3. The 3rd argument passed to this constructor should be a function that returns an
   *    array of fields for your entity's CRUD actions.
   * 4. For non-crud actions that need a different set of fields, you can override the
   *    list from step 3 on a per-action basis by defining a fields() method in that action.
   *    See for example BasicGetFieldsAction::fields() or GetActions::fields().
   *
   * @param Result $result
   * @throws \Civi\API\Exception\NotImplementedException
   */
  public function _run(Result $result) {
    try {
      $actionClass = ActionUtil::getAction($this->getEntityName(), $this->action);
    }
    catch (NotImplementedException $e) {
    }
    if (isset($actionClass) && method_exists($actionClass, 'fields')) {
      $values = $actionClass->fields();
    }
    else {
      $values = $this->getRecords();
    }
    $this->padResults($values);
    $result->exchangeArray($this->queryArray($values));
  }

  /**
   * Ensure every result contains, at minimum, the array keys as defined in $this->fields.
   *
   * Attempt to set some sensible defaults for some fields.
   *
   * In most cases it's not necessary to override this function, even if your entity is really weird.
   * Instead just override $this->fields and thes function will respect that.
   *
   * @param array $values
   */
  protected function padResults(&$values) {
    $fields = array_column($this->fields(), 'name');
    foreach ($values as &$field) {
      $defaults = array_intersect_key([
        'title' => empty($field['name']) ? NULL : ucwords(str_replace('_', ' ', $field['name'])),
        'entity' => $this->getEntityName(),
        'required' => FALSE,
        'options' => !empty($field['pseudoconstant']),
        'data_type' => \CRM_Utils_Array::value('type', $field, 'String'),
      ], array_flip($fields));
      $field += $defaults;
      if (!$this->loadOptions && isset($defaults['options'])) {
        $field['options'] = (bool) $field['options'];
      }
      $field += array_fill_keys($fields, NULL);
    }
  }

  /**
   * @return string
   */
  public function getAction() {
    return $this->action;
  }

  public function fields() {
    return [
      [
        'name' => 'name',
        'data_type' => 'String',
      ],
      [
        'name' => 'title',
        'data_type' => 'String',
      ],
      [
        'name' => 'description',
        'data_type' => 'String',
      ],
      [
        'name' => 'default_value',
        'data_type' => 'String',
      ],
      [
        'name' => 'required',
        'data_type' => 'Boolean',
      ],
      [
        'name' => 'required_if',
        'data_type' => 'String',
      ],
      [
        'name' => 'options',
        'data_type' => 'Array',
      ],
      [
        'name' => 'data_type',
        'data_type' => 'String',
      ],
      [
        'name' => 'input_type',
        'data_type' => 'String',
      ],
      [
        'name' => 'input_attrs',
        'data_type' => 'Array',
      ],
      [
        'name' => 'fk_entity',
        'data_type' => 'String',
      ],
      [
        'name' => 'serialize',
        'data_type' => 'Integer',
      ],
      [
        'name' => 'entity',
        'data_type' => 'String',
      ],
    ];
  }

}
