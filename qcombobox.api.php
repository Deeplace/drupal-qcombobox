<?php

/**
 * @file
 * Hooks provided by the Qcombobox module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Define data source for qcombobox form element.
 *
 * Request types can use parameters to customize the behavior of the data source.
 * - 'xxx' request is invoked in AJAX requests when user types in the qcombobox field.
 *   It can use $search_str to filter the results, $element["#options_source"]
 *   provided additional parameters, global context ($user, $language, etc), and
 *   $_GET parameters passed in the AJAX request through $element["#extra_params"]
 *   property.
 * - 'xxx_byid' request is invoked to retrieve single value by its unique ID
 *   in the rendering phase of the form. It should return single value, or NULL.
 *   It should not use $search_str for item lookup, and $element["#options_source"]
 *   for filtering, but should not rely on any global context or $_GET parameters.
 * - 'xxx_validate' request is invoked to validate that the value entered by user
 *  exists in the data source, and is accessible to the user. It should return
 *  single value, or NULL/FALSE if the value is invalid. It should use $search_str
 *  for item lookup, $element["#options_source"] for filtering, global context ($user,
 *  $language, etc), but should not rely on any $_GET parameters (because there
 *  is no AJAX request in progress).
 *
 * @param string $request
 *  The class of data requested. Modules may define multiple classes of data.
 *  Each class should implement 3 requests:
 *  - 'xxx' - to retrieve list of possible values, optionally filtered by
 *   $search_str.
 *  - 'xxx_byid' - to retrieve single value by its unique ID, passed in $search_str.
 *  - 'xxx_validate' - to validate that the value passed in $search_str exists,
 * @param string $search_str
 *   A partial string to filter the results by, or a unique ID to look up.
 * @param string $param1..N
 *   Additional parameters that may be used to customize the behavior of
 * the data source. Their meaning is specific to each data source. These parameters
 * are passed directly from the qcombobox form element definition, in the
 * #options_source property  as array.
 *
 * @return
 *  For 'xxx' request - an associative array of key => value pairs representing
 * the possible options.
 *
 */
function hook_qcombobox_source($request, $search_str = '', $param1 = 'value1', $param2 = 'value2') {
  if ($request == 'currency') {
    global $user;
    $fields = drupal_schema_fields_sql('currencies');
    if (!in_array($param1, $fields)) {
      $param1 = 'iso_code';
    }
    if (!in_array($param2, $fields)) {
      $param2 = 'title';
    }

    $query = db_select('currencies', 'c')
      ->fields('c', array($param1, $param2))
      ->orderBy('c.weight')
      ->orderBy('c.iso_code');

    if ($search_str) {
      $query->condition("c.$param2", db_like($search_str) . '%', 'LIKE');
    }
    if ($_GET['some_filter']) {
      $query->condition('c.some_field', $_GET['some_filter']);
    }
    $query->condition('c.uid', $user->uid, '<>');

    $result = $query->execute();

    $output = array();
    while ($row = $result->fetchObject()) {
      $output[$row->$key] = $row->$value;
    }

    return $output;
  }
  elseif ($request === 'currency_byid') {
    $fields = drupal_schema_fields_sql('currencies');
    if (!in_array($param1, $fields)) {
      $param1 = 'iso_code';
    }
    if (!in_array($param2, $fields)) {
      $param2 = 'title';
    }
    $search_str = str_pad($search_str, 3, '0', STR_PAD_LEFT);
    return db_query("SELECT $param2 FROM {currencies} WHERE $param1 = :str LIMIT 1", array(':str' => $search_str))->fetchField();
  }
  elseif ($request === 'currency_validate') {
    global $user;
    $fields = drupal_schema_fields_sql('currencies');
    if (!in_array($param1, $fields)) {
      $param1 = 'iso_code';
    }
    if (!in_array($param2, $fields)) {
      $param2 = 'title';
    }

    $query = db_select('currencies', 'c')
      ->fields('c', array($param2))
      ->condition("c.$param1", $search_str)
      ->condition('c.uid', $user->uid, '<>');

    $result = $query->execute();

    return $result->fetchField();
  }
}
