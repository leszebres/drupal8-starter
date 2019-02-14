<?php

use Drupal\drup\DrupCommon;

/**
 * Implements hook_preprocess_menu().
 * @param $variables
 */
function drup_preprocess_menu(&$variables) {
    $language = Drupal::languageManager()->getCurrentLanguage(LanguageInterface::TYPE_CONTENT)->getId();

    // Unset untranslated menu items
    if (isset($variables['menu_name']) && in_array($variables['menu_name'], ['main', 'secondary', 'footer'])) {
        foreach ($variables['items'] as $menuId => $menuItem) {
            if (!$variables['items'][$menuId] = DrupCommon::checkMenuItemTranslation($menuItem, $language)) {
                unset($variables['items'][$menuId]);
            }
        }
    }
}