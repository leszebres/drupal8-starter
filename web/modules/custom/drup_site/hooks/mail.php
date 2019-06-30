<?php

use Drupal\drup_settings\DrupSettings;

/**
 * @inheritdoc
 */
function drup_site_mail_alter(&$message) {
    $siteMail = \Drupal::config('system.site')->get('mail');

    if ($message['headers']['Sender'] === $siteMail) {
        /** @var \Drupal\drup_settings\DrupSettings $drupSettings */
        $drupSettings = \Drupal::service('drup_settings');

        if ($from = $drupSettings->getValue('site_emails_from')) {
            $message['from'] = $message['reply-to'] = $message['headers']['Sender'] = $message['headers']['Return-Path'] = $from;
            $message['headers']['From'] = str_replace($siteMail, $from, $message['headers']['From']);
        }
    }
}

/**
 * @inheritdoc
 */
function drup_site_preprocess_swiftmailer(&$variables) {
    $theme = \Drupal::theme()->getActiveTheme();
    $variables['logo'] = $variables['base_url'] . '/' . $theme->getPath() . '/images/logo-mail.png';

    /** @var \Drupal\drup_settings\DrupSettings $drupSettings */
    $drupSettings = \Drupal::service('drup_settings');
    $variables['site_name'] = $drupSettings->getValue('site_name');

//    echo '<pre>';
//    var_dump($variables);
//    echo '</pre>';
//    die;
}
