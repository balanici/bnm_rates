<?php
/**
 * Form builder for bnm_rates_settings_form form.
 *
 * @see system_settings_form()
 */
function bnm_rates_settings_form($form_state) {
  global $language;
  $lang = $language->language;


  $currencies = bnm_rates_currency_list($lang);

  $form = array();
  $form['currency_items']['#tree'] = TRUE;

  foreach ($currencies as $currency) {
    $form['currency_items'][$currency->valute_id] = array(
      'currency_name' => array(
        '#markup' => check_plain($currency->currency_name),
      ),
      'char_code' => array(
        '#markup' => check_plain($currency->char_code),
      ),
      'in_block' => array(
        '#type' => 'textfield',
        '#default_value' => check_plain($currency->in_block),
        '#size' => 5,
        '#maxlength' => 1,
      ),
      'weight' => array(
        '#type' => 'weight',
        '#title' => t('Weight'),
        '#default_value' => $currency->weight,
        '#delta' => 50,
        '#title_display' => 'invisible',
      ),
    );
  }

  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array('#type' => 'submit', '#value' => t('Save Changes'));
  return $form;
}

function theme_bnm_rates_settings_form($variables) {
  $form = $variables['form'];
  $rows = array();

  foreach (element_children($form['currency_items']) as $valute_id) {
    $form['currency_items'][$valute_id]['weight']['#attributes']['class'] = array('currency-item-weight');
    $rows[] = array(
      'data' => array(
        drupal_render($form['currency_items'][$valute_id]['currency_name']),
        drupal_render($form['currency_items'][$valute_id]['char_code']),
        drupal_render($form['currency_items'][$valute_id]['in_block']),
        drupal_render($form['currency_items'][$valute_id]['weight']),
      ),
      'class' => array('draggable'),
    );

  }

  $header = array(t('Currency Name'), t('Code'), t('Show in block'), t('Weight'));

  $table_id = 'currency-items-table';

  $output = theme('table', array(
    'header' => $header,
    'rows' => $rows,
    'attributes' => array('id' => $table_id),
  ));

  $output .= drupal_render_children($form);

  // We now call the drupal_add_tabledrag() function in order to add the
  // tabledrag.js goodness onto our page.
  //
  // For a basic sortable table, we need to pass it:
  // - the $table_id of our <table> element,
  // - the $action to be performed on our form items ('order'),
  // - a string describing where $action should be applied ('siblings'),
  // - and the class of the element containing our 'weight' element.
  drupal_add_tabledrag($table_id, 'order', 'sibling', 'currency-item-weight');

  return $output;
}

function bnm_rates_settings_form_submit($form, &$form_state) {
  foreach ($form_state['values']['currency_items'] as $id => $item) {
    db_query("UPDATE {bnm_currency} SET weight = :weight, in_block = :in_block WHERE valute_id = :id",
              array(':weight' => $item['weight'], ':in_block' => $item['in_block'] , ':id' => $id)
    );
  }
}


function bnm_rates_currency_list($lang = 'en') {
  if (in_array($lang, array('ro', 'mo'))) {
    $lang = 'md';
  }
  $query = "SELECT valute_id, char_code, currency_name, lang, in_block, weight
            FROM {bnm_currency}
            WHERE lang = :lang
            ORDER BY weight";
  $result = db_query($query, array(':lang' => $lang))->fetchAll();

  return $result;
}
