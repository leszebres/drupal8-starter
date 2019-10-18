<?php

use Drupal\drup\Helper\DrupUrl;

/**
 * @param $variables
 */
function drup_theme_preprocess_menu(&$variables) {
    if (isset($variables['menu_name'])) {
        if ($variables['menu_name'] === 'main') {
//            /** @var \Drupal\drup\DrupPageEntity $currentEntity */
//            $currentEntity = \Drupal::service('drup_page_entity');
//            /** @var \Drupal\drup_router\DrupRouter $drupRouter */
//            $drupRouter = \Drupal::service('drup_router');
//
//            //$regionNid = $drupRouter->getId('rpl');
//
//            // Applique la classe is-active pour bundle => routeName
//            $customActiveTrails = [
//                'news' => 'rpl'
//            ];
//            $currentCustomActiveTrail = ($currentEntity !== null && isset($customActiveTrails[$currentEntity->bundle()])) ? $customActiveTrails[$currentEntity->bundle()] : null;
//
//            //
//            foreach ($variables['items'] as $index => &$item) {
//                // Classes
//                if ($entity = DrupUrl::loadEntity($item['url'])) {
//                    if ($entity->bundle() === 'page' && $regionNid === $entity->id()) {
//                        $item['attributes']->addClass('theme-secondary');
//                    }
//                    // Custom active trail
//                    if ($currentCustomActiveTrail !== null && $drupRouter->getId($currentCustomActiveTrail) === $entity->id()) {
//                        $item['in_active_trail'] = true;
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
    $lastItem = end($variables['breadcrumb']);

    // Dans les pages système, le dernier item (titre du lien de menu de la page courante) n'est pas
    // généré par le module Menu breadcrumb, on affiche donc le page title
    if (!empty($lastItem) && !empty($lastItem['url'])) {
        $request     = \Drupal::request();
        $route_match = \Drupal::routeMatch();

        if ($title = \Drupal::service('title_resolver')->getTitle($request, $route_match->getRouteObject())) {
            $variables['breadcrumb'][] = ['text' => $title];
        }
    }

    $variables['#cache']['contexts'][] = 'url.path'; // invalid cache
    $variables['#cache']['contexts'][] = 'url';
}
