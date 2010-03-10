<?php
  /**
   * Genereates an opening form tag
   */
  function krudt_html_form_tag($method = 'post', $action = null) {
    $method = strtolower($method);
    $action = $action ? $action : url();
    $html = "";
    $html .= '<form method="' . htmlentities($method === 'get' ? 'get' : 'post') . '" action="' . htmlentities($action) . '">';
    if ($method !== 'get' && $method !== 'post') {
      $html .= '<input type="hidden" name="_method" value="' . $method . '" />';
    }
    return $html;
  }

  /**
   * Genereates an opening form closing tag
   */
  function krudt_form_tag_end()
  {
      return '</form>';
  }

  /**
   * Generates cancel/submit panel for forms.
   */
  function krudt_form_footer($submit_title = 'OK', $href_back = null) {
    $href_back = $href_back ? $href_back : url();
    $html = "";
    $html .= "\n" . '<p class="form-footer">';
    $html .= "\n" . '<a href="' . htmlentities($href_back) . '">Cancel</a>';
    $html .= "\n" . ':';
    $html .= "\n" . '<input type="submit" value="' . htmlentities($submit_title) . '" />';
    $html .= "\n" . '</p>';
    return $html;
  }

  /**
   * Renders global errors for an entity.
   */
  function krudt_errors($entity) {
    $html = "";
    foreach ($entity->errors as $field => $error) {
      if (!is_string($field)) {
        $html .= "\n" . '<p style="color:red">' . htmlentities($error) . '</p>';
      }
    }
    return $html;
  }

  /**
   * Creates a `<input type="text" />` for a record.
   */
  function krudt_html_text_field($entry, $field, $label = null) {
    $label || $label = ucfirst(str_replace('_', ' ', $field));
    $html = '  <p class="krudt-form">
    <label for="field-' . htmlentities($field) . '">' . htmlentities($label) . '</label>
    <input type="text" id="field-' . htmlentities($field) . '" name="' . htmlentities($field) . '" value="' . htmlentities($entry->{$field}()) . '" />
';
    if (isset($entry->errors[$field])) {
      $html .= '    <span style="display:block;color:red">' . htmlentities($entry->errors[$field]) . ' </span>
';
    }
    $html .= "  </p>\n";
    return $html;
  }

  /**
   * Creates a `<table>` containing a collection.
   */
  function krudt_collection($context, $collection, $slug, $fields = null, $row_actions = null, $collection_actions = null) {
    return new krudt_view_CollectionWidget($context, $collection, $slug);
  }

  /**
   * Creates a pagination widget for a collection.
   */
  function krudt_paginate($context, $collection, $size = 10) {
    return new krudt_view_SimplePaginateWidget($context, $collection, $size);
  }