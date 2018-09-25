<?php

/**
 * Implements hook_preprocess_HOOK() for Block document templates.
 */
function drup_theme_preprocess_block(array &$variables) {
    // Do not display id attribute
    unset($variables['attributes']['id']);
    $variables['add_container'] = false;
    
    $pluginId = $variables['base_plugin_id'];
    $derivativeId = $variables['derivative_plugin_id'];
    
    // Main menu
    if (in_array($pluginId, ['system_menu_block', 'menu_block'])) {
        $variables['attributes']['class'] = [
            'nav',
            'nav--' . $derivativeId,
        ];
        $variables['attributes']['id'] = 'nav-' . $derivativeId;
        
        if ($derivativeId === 'footer') {
            $currentYear = date('Y');
            $siteConfig = \Drupal::config('system.site');
            $siteName = $siteConfig->get('name');
            $variables['block_after'] = '© '.$currentYear.' '.$siteName.'. '.t('All rights reserved').'.';
        }
    }
    // Custom Blocs
    elseif (strpos('drup_', $pluginId) !== false) {
        $variables['add_container'] = true;
        $variables['attributes']['class'] = [
            'block',
            'block--' . $derivativeId,
        ];
    }
}
