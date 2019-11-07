<?php

use Drupal\drup\DrupMenu;

/**
 * {@inheritdoc}
 */
function drup_site_preprocess_menu(&$variables) {
    $languageId = Drupal::languageManager()->getCurrentLanguage()->getId();

    // Unset untranslated menu items
    if (isset($variables['menu_name']) && in_array($variables['menu_name'], ['main', 'secondary', 'footer'])) {
        $variables['items'] = DrupMenu::translate($variables['items'], $languageId);
    }

    // Menu Admin
    if (($variables['theme_hook_original'] === 'menu__toolbar__admin') && !\in_array('super_admin', $variables['user']->getRoles())) {
        // Global
        unset($variables['items']['system.admin_config']);
        if (!empty($variables['items']['system.admin_structure']['below'])) {
            unset(
                $variables['items']['system.admin_structure']['below']['block.admin_display'],
                $variables['items']['system.admin_structure']['below']['entity.menu.collection']['below']['entity.menu.add_form']
            );

            // Taxonomies
            $disableTaxonomies = [];
            if (!empty($variables['items']['system.admin_structure']['below']['entity.taxonomy_vocabulary.collection']['below'])) {
                unset($variables['items']['system.admin_structure']['below']['entity.taxonomy_vocabulary.collection']['below']['entity.taxonomy_vocabulary.add_form']);

                foreach ($variables['items']['system.admin_structure']['below']['entity.taxonomy_vocabulary.collection']['below'] as $route => $item) {
                    $variables['items']['system.admin_structure']['below']['entity.taxonomy_vocabulary.collection']['below'][$route]['is_expanded'] = false;

                    if (!empty($disableTaxonomies)) {
                        foreach ($disableTaxonomies as $disableTaxonomy) {
                            if ($route === 'entity.taxonomy_vocabulary.overview_form.' . $disableTaxonomy) {
                                unset($variables['items']['system.admin_structure']['below']['entity.taxonomy_vocabulary.collection']['below'][$route]);
                            }
                        }
                    }
                }
            }
        }
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

    // Item Médias
    if (isset($links['admin_toolbar_tools.extra_links:media_page'])) {
        $links['admin_toolbar_tools.extra_links:media_page']['parent'] = 'system.admin';
        $links['admin_toolbar_tools.extra_links:media_page']['weight'] = '-8';
        $links['admin_toolbar_tools.extra_links:add_media']['parent'] = 'admin_toolbar_tools.extra_links:media_page';
    }

    // Suppresion du liens "Fichiers"
    if (isset($links['admin_toolbar_tools.extra_links:view.files'])) {
        unset($links['admin_toolbar_tools.extra_links:view.files']);
    }
}

/**
 * Implements hook_toolbar_alter().
 */
function drup_site_toolbar_alter(&$items) {
    $items['administration']['#attached']['library'][] = 'drup_site/toolbar.tree';
    $items['administration_search']['tray']['search']['#title'] = t($items['administration_search']['tray']['search']['#title']);
}
