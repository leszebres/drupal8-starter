<?php

use Drupal\drup\Entity\Node;
use Drupal\drup\Media\DrupMediaImage;
use Drupal\drup\Views\DrupViewsRows;
use Drupal\drup\Views\DrupViewsView;

/**
 * Preprocess View
 *
 * @param $variables
 */
function drup_theme_preprocess_views_view(&$variables) {
    $drupRouter = \Drupal::service('drup_router');
    $drupRouteName = $drupRouter->getName();

    $drupViewsView = new DrupViewsView($variables);
    $variables = $drupViewsView->controller();

    if (empty($variables['view']->total_rows)) {
        $variables['empty'] = t('No result match your search.');
    }
    
//    switch ($variables['view']->id()) {
//        case 'store_locators':
//            $variables['display_total_rows_count'] = \Drupal::translation()->formatPlural(
//                $variables['view']->total_rows,
//                '@count result founded.',
//                '@count results founded.'
//            );
//            if (empty($variables['rows'])) {
//                $variables['empty'] = t('No result match your search.');
//            }
//            break;
//    }
}

/**
 * Preprocess Rows
 *
 * @param $variables
 */
function drup_theme_preprocess_views_view_unformatted(&$variables) {
    $drupViewsRows = new DrupViewsRows($variables);
    $variables = $drupViewsRows->controller();
}

/**
 * Suggestions views
 * @param array $suggestions
 * @param array $variables
 */
function drup_theme_theme_suggestions_views_view_unformatted_alter(array &$suggestions, array &$variables) {
    switch ($variables['view']->id()) {
        case 'news':
            $variables['theme_hook'] = 'primary'; // hook_id
            $suggestions[] = 'views_view_unformatted__primary'; // template twig
            break;
    }
}


/**
 * Toutes les vues
 */
//function drup_theme_views_rows_controller($variables) {
//
//    return $variables;
//}

/**
 * Vue rows ""
 */
//function drup_theme_views_rows_controller__THEMEPARTIAL($variables) {
//
//    return $variables;
//}

/**
 * All views
 * @param $variables
 *
 * @return mixed
 */
function drup_theme_views_rows_controller($variables) {
    $viewId = $variables['view']->id();
    $displayId = $variables['view']->current_display;

    // Rows : valeurs par défaut liées au thème
    $variables['row_layout'] = 'third';
    $variables['row_image_style'] = 'list_item_primary';
    $variables['row_tag_title'] = 'h3';

    if ($viewId === 'news') {
        switch ($displayId) {
            case 'similar_content':
                $variables['row_layout'] = 'quarter';
                break;
        }
    }

    return $variables;
}

/**
 * View rows primary
 * @param $variables
 *
 * @return mixed
 */
function drup_theme_views_rows_controller__primary($variables) {
    $variables['rows_attributes']->addClass('list--primary');

    if (!empty($variables['view']->result)) {
        foreach ($variables['view']->result as $id => &$result) {
            $result->_entity = Node::load($result->_entity->id());
            $result->drupField = $result->_entity->drupField();
            
            $result->layout = $variables['row_layout'];
            $result->imageStyle = $variables['row_image_style'];
            $result->tagTitle = $variables['row_tag_title'];

            /** @var \Drupal\Core\Template\Attribute $attributes **/
            $attributes = $variables['rows'][$id]['attributes'];
            $attributes->addClass('list-item');
    
            // Media
            $mediaField = $result->drupField->getValue('thumbnail', 'target_id');
            if ($mediaField && !empty($result->imageStyle)) {
                $mediaEntity = new DrupMediaImage($mediaField);

                if ($medias = $mediaEntity->renderMedias($result->imageStyle)) {
                    $result->media = current($medias);
                    $attributes->addClass('has-media');
                }
            }
    
            // partial
            if (isset($result->partial)) {
                $attributes->addClass('l-'.$result->partial);
            }
            // Layout
            if (!empty($result->layout)) {
                $attributes->addClass('l-' . $result->layout);
            }

            // Content
            $result->title = $result->_entity->getTitle();
            $result->url = $result->_entity->toUrl();
            $result->cta = t('Read more');

            // Add content
            $variables['rows'][$id]['content'] = $result;
            $variables['rows'][$id]['attributes'] = $attributes;
        }
    }

    return $variables;
}


/**
 * Implements hook_preprocess_views_infinite_scroll_pager().
 * @param $vars
 */
function drup_theme_preprocess_views_infinite_scroll_pager(&$vars) {
    $vars['btn_classes'] = 'btn';
    
    //if (isset($vars['parameters']['field_localisation_proximity'])) {
    //    $vars['btn_classes'] .= ' btn--link';
    //} else {
        $vars['btn_classes'] .= ' btn--primary';
    //}
}
