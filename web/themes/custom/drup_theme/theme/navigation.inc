<?php

use Drupal\drup\DrupPageEntity;
use Drupal\drup\Helper\DrupUrl;

/**
 * @param $variables
 */
function drup_theme_preprocess_menu(&$variables) {
    if (isset($variables['menu_name'])) {
        if ($variables['menu_name'] === 'main') {
//            $currentEntity = DrupPageEntity::loadEntity();
//            $drupRouter = \Drupal::service('drup_router');
//
//            $regionNid = $drupRouter->getId('rpl');
//
//            foreach ($variables['items'] as $index => &$item) {
//                // Classess
//                if ($entity = DrupUrl::loadEntity($item['url'])) {
//                    if ($entity->bundle() === 'page' && $regionNid === $entity->id()) {
//                        $item['attributes']['class'] .= ' theme-secondary';
//                    }
//                }
//            }
        }
    }
}

/**
 * @param $variables
 */
function drup_theme_preprocess_breadcrumb(&$variables) {
    $request     = \Drupal::request();
    $route_match = \Drupal::routeMatch();

    if ($title = \Drupal::service('title_resolver')->getTitle($request, $route_match->getRouteObject())) {
        $variables['breadcrumb'][] = ['text' => $title];
    }

    $variables['#cache']['contexts'][] = 'url.path'; // invalid cache
    $variables['#cache']['contexts'][] = 'url';
}
