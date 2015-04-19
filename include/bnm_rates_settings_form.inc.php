<?php
/**
 * Created by PhpStorm.
 * User: igor
 * Date: 4/19/15
 * Time: 7:26 PM
 */



/**
 * Form builder for bnm_rates_settings_form form.
 *
 * @see system_settings_form()
 */
function bnm_rates_settings_form() {
  $rows = array(0, 1, 2, 3, 4,5,6,7,8,9);


  $form = array();

  $form['bnm_rates_settings'] = array(
    '#type' => 'textfield',
    '#title' => 'Greeting message',
    '#default_value' => variable_get('eureka_greeting', 'Hello World!'),
    '#size' => 60,
    '#maxlength' => 255,
    '#description' => 'Please input the greeting message.',
    '#required' => TRUE,
  );

  return system_settings_form($form);
}