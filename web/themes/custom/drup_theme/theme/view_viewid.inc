<?php

/**
 * Vue ""
 */
//function drup_theme_views_rows_controller__VIEWID($variables) {
//
//    return $variables;
//}

/**
 * Vue + Display ""
 */
//function drup_theme_views_rows_controller__VIEWID__DISPLAYID($variables) {
//    $aliasManager = \Drupal::service('path.alias_manager');
//
//    $variables['block']['title']['value'] = t('Block title');
//    $variables['rows_attributes']->addClass('list--primary');
//
//    if (!empty($variables['view']->result)) {
//        foreach ($variables['view']->result as $id => &$result) {
//            $node = $result->_entity;
//            $drupField = new DrupField($node);
//
//            // Row attributes
//            $attributes = $variables['rows'][$id]['attributes'];
//            $attributes->addClass('list-item');
//
//            // Content
//            $result->title = $node->getTitle();
//            $result->url = $aliasManager->getAliasByPath('/node/' . $node->id());
//
//            // Add content
//            $variables['rows'][$id]['content'] = $result;
//            $variables['rows'][$id]['attributes'] = $attributes;
//        }
//    }
//
//    return $variables;
//}