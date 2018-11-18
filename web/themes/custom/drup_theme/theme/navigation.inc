<?php

use Drupal\node\Entity\Node;

use Drupal\drup\DrupCommon;
use Drupal\drup\DrupEntityField;

/**
 * @param $variables
 */
function drup_theme_preprocess_menu(&$variables) {
    if (isset($variables['menu_name'])) {
        if ($variables['menu_name'] === 'main') {
//            $drupRouter = \Drupal::service('drup_router.router');
//
//            foreach ($variables['items'] as $index => &$item) {
//
//            }
        }
    }
}

/**
 * @param $variables
 */
function drup_theme_preprocess_breadcrumb(&$variables) {
    if ($variables['breadcrumb'] && $title = DrupCommon::getCurrentTitle()) {
        $variables['breadcrumb'][] = ['text' => $title];
    }

    $variables['#cache']['contexts'][] = 'url.path'; // invalid cache
    $variables['#cache']['contexts'][] = 'url';
}
