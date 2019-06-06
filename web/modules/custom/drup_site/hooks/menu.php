<?php

use Drupal\drup\DrupMenu;

/**
 * @inheritdoc
 */
function drup_site_preprocess_menu(&$variables) {
    $languageId = Drupal::languageManager()->getCurrentLanguage()->getId();

    // Unset untranslated menu items
    if (isset($variables['menu_name']) && in_array($variables['menu_name'], ['main', 'secondary', 'footer'])) {
        $variables['items'] = DrupMenu::translate($variables['items'], $languageId);
    }
}

/**
 * Overrides menu toolbar
 *
 * @param $links
 */
function drup_site_menu_links_discovered_alter(&$links) {
    // Debug :
    // See table "router" to find route name for admin path
    // ex : /admin/people/permissions <=> user.admin_permissions

    // Mediatheque dans "Contenus"
    if (isset($links['admin_toolbar_tools.media_page'])) {
        $links['admin_toolbar_tools.media_page']['title'] = t('Mediatheque');
    }

    // Suppresion de "Fichiers"
    if (isset($links['admin_toolbar_tools.view.files'])) {
        unset($links['admin_toolbar_tools.view.files']);
    }

    // Move DrupSettings in top level
    $links['drup_admin_toolbar.drup_settings'] = [
        'title' => t('Site configuration'),
        'route_name' => 'drup_settings.admin_form',
        'menu_name' => 'admin',
        'parent' => 'system.admin',
        'weight' => 50
    ];
}

/**
 * Implements hook_toolbar_alter().
 */
function drup_site_toolbar_alter(&$items) {
    $items['administration']['#attached']['library'][] = 'drup_site/toolbar.tree';
}