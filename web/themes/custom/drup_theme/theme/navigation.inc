<?php

/**
 * @param $variables
 */
function drup_theme_preprocess_menu(&$variables) {
    if (isset($variables['menu_name'])) {
        if ($variables['menu_name'] === 'main') {
//            $currentEntity = DrupCommon::getPageEntity();
//            $drupRouter = \Drupal::service('drup_router.router');
//
//            foreach ($variables['items'] as $index => &$item) {
//                if ($menuItemNid = DrupCommon::getNidFromMenuItem($item)) {
//                    if ($menuItemNid === $routeProductsId) {
//                        if (in_array($currentEntity->bundle, [ProductsHelpers::$bundle, FamiliesHelpers::$bundle])) {
//                            $item['attributes']['class'] = 'is-current';
//                        }
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
