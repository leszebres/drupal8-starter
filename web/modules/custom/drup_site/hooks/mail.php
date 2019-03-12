<?php

use Drupal\drup_settings\DrupSettings;

/**
 * @inheritdoc
 */
function drup_site_preprocess_swiftmailer(&$variables) {
    $theme = \Drupal::theme()->getActiveTheme();
    $variables['logo'] = $variables['base_url'] . '/' . $theme->getPath() . '/images/logo-mail.png';

    $drupSettings = new DrupSettings();
    $variables['site_name'] = $drupSettings->getValue('site_name');

//    echo '<pre>';
//    var_dump($variables);
//    echo '</pre>';
//    die;
}
