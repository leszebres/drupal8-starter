<?php

/**
 * @param $variables
 */
function drup_preprocess_swiftmailer(&$variables) {
    $theme = \Drupal::theme()->getActiveTheme();
    $variables['logo'] = $variables['base_url'] . '/' . $theme->getPath() . '/images/logo-mail.png';

    $drupSettings = new \Drupal\drup_settings\DrupSettings();
    $variables['site_name'] = $drupSettings->getValue('site_name');

//    echo '<pre>';
//    var_dump($variables);
//    echo '</pre>';
//    die;
}