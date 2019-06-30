<?php

use Drupal\drup_settings\DrupSettings;

/**
 * @inheritdoc
 */
function drup_theme_preprocess_block(array &$variables) {
    // Do not display id attribute
    unset($variables['attributes']['id']);
    $variables['add_container'] = false;
    
    $pluginId = $variables['base_plugin_id'];
    $derivativeId = $variables['derivative_plugin_id'];
    
    // Logo
    if ($pluginId === 'system_branding_block') {
        /** @var \Drupal\drup_settings\DrupSettings $drupSettings */
        $drupSettings = \Drupal::service('drup_settings');

        $variables['site_alt_logo'] = $drupSettings->getValue('site_logo_alt');
        $variables['home_h1'] = $drupSettings->getValue('home_h1');
        $variables['is_front'] = \Drupal::service('path.matcher')->isFrontPage();
    }
    // Breadcrumb
    elseif ($pluginId === 'system_breadcrumb_block') {
        $variables['elements']['#cache']['max-age'] = $variables['#cache']['max-age'] = 0;
    }
    
    // Main menu
    elseif (in_array($pluginId, ['system_menu_block', 'menu_block'])) {
        $variables['attributes']['class'] = [
            'nav',
            'nav--' . $derivativeId,
        ];
        $variables['attributes']['id'] = 'nav-' . $derivativeId;
    }
}
