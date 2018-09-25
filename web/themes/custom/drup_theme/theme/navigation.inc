<?php

use Drupal\node\Entity\Node;

use Drupal\drup\DrupCommon;
use Drupal\drup\DrupEntityField;

/**
 * Implements hook_preprocess_menu().
 *
 * Workaround for randomly lost menu item options: load link entities and
 * restore lost link attributes.
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
        //dsm(DrupCommon::checkPageEntity());
    if ($variables['breadcrumb'] && $title = DrupCommon::getCurrentTitle()) {
        $variables['breadcrumb'][] = ['text' => $title];
    }
    $variables['#cache']['contexts'][] = 'url.path'; // invalid cache
    $variables['#cache']['contexts'][] = 'url';
}

/**
 * Implements hook_preprocess_menu_local_task().
 */
function drup_theme_preprocess_menu_local_task(array &$variables) {
    //$variables['element']['#link']['url']->setOption('attributes', ['class'=>'rounded']);
}
