<?php

/**
 * @param $arg
 * @return mixed
 */
function bnm_rates_probe($arg) {
    return $arg;
}

/**
 * Retrieves xmldata from bnm.org
 *
 * @param string $date
 * @param string $lang
 * @return SimpleXMLElement
 *
 * @author idoctor
 */
function bnm_rates_pull_xmldata($date = '', $lang = 'ru') {
    //link http://www.bnm.md/md/official_exchange_rates?get_xml=1&date=20.01.2015
    //just for today if empty
    if (empty($date)) {
        $date = date("d.m.Y");
    }
    print_r($date);
    $url = "http://www.bnm.md/{$lang}/official_exchange_rates?get_xml=1&date={$date}";

    //for dev
//    $url = drupal_get_path('module', 'bnm_rates') . '/official_exchange_rates.xml';

    $simple_xml = simplexml_load_file($url);
    if ($simple_xml) {
        $result = bnm_rates_store_data($simple_xml, $lang);
        watchdog('bnm_rates', "Store rates for {$date} lang={$lang}. Result: {$result}");
    } else {
        watchdog('bnm_rates', "SimpleXML NULL or FALSE for {$date} lang={$lang}.");
    }

    return $simple_xml;
}

/**
 * Stores xmldata into database
 */
function bnm_rates_store_data($simple_xml, $lang) {
    $attribs = $simple_xml->attributes();
    $valute_array = $simple_xml->children();
    var_dump($attribs);
    foreach ($valute_array as $valute) {
        $attr = $valute->attributes();
        $valute_id = (string)$attr['ID'];
        $currency_record = db_merge('bnm_currency')
            ->key(array(
                'valute_id' => (int) $valute_id,
                'lang' => $lang,
            ))
            ->fields(
                array(
                    'num_code' => (string) $valute->NumCode,
                    'char_code' => (string) $valute->CharCode,
                    'nominal' => (int) $valute->Nominal,
                    'currency_name' => (string) $valute->Name,
//                    'lang' => $lang,
                ))
            ->execute();

        $rates_record = db_merge('bnm_exchange_rate')
            ->key(array(
                'valute_id' => (int) $valute_id,
                'date' => (string) $attribs['Date'],
            ))
            ->fields(array(
                'value' => (float) $valute->Value
            ))
            ->execute();
    }

}

function bnm_rates_select($date = '', $lang = 'en') {

}
