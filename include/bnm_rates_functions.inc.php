<?php

/**
 * @param $arg
 * @return mixed
 */
function bnm_rates_probe($arg) {
    return $arg;
}

function bnm_rates_get_xmlobj($date = 'rewuired', $lang = 'en') {
    //link http://www.bnm.md/md/official_exchange_rates?get_xml=1&date=20.01.2015
    //just for today:
    $date = '22.01.2015';
    $url = "http://www.bnm.md/{$lang}/official_exchange_rates?get_xml=1&date={$date}";
    $url = drupal_get_path('module', 'bnm_rates') . '/official_exchange_rates.xml';
    $xml = simplexml_load_file($url);
    return $xml;
}
