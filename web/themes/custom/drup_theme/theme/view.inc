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
    /** @var \Drupal\drup_router\DrupRouter $drupRouter */
    $drupRouter = \Drupal::service('drup_router');
    $drupRouteName = $drupRouter->getName();

    $drupViewsView = new DrupViewsView($variables);
    $variables = $drupViewsView->controller();

    if (empty($variables['rows'])) {
        $variables['empty'] = t('No result match your search.');
    }

    switch ($variables['view']->id() . '_' . $variables['view']->current_display) {
        case 'news_list_all':
        case 'publications_list_all':
            $variables['display_total_rows_count'] = \Drupal::translation()->formatPlural(
                $variables['view']->total_rows,
                '@count result founded.',
                '@count results founded.'
            );
            if (empty($variables['rows'])) {
                $variables['empty'] = t('No result match your search.');
            }
            break;
    }
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
    $variables['row_layout'] = 'third'; // .list-item.l-XX
    $variables['rows_partial'] = 'primary'; // .list--XX
    $variables['row_partial'] = 'primary'; // tpl list item
    $variables['row_image_style'] = 'item_primary';
    $variables['row_tag_title'] = 'h3';
    $variables['row_cta'] = t('Read more');

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
    $variables['rows_attributes']->addClass('list--' . $variables['rows_partial']);

    if (!empty($variables['view']->result)) {
        foreach ($variables['view']->result as $id => &$result) {
            $result->_entity = Node::load($result->_entity->id());
            /** @var \Drupal\drup\Entity\DrupField $drupField */
            $result->drupField = $result->_entity->drupField();

            $result->layout = $variables['row_layout'];
            $result->imageStyle = $variables['row_image_style'];
            $result->tagTitle = $variables['row_tag_title'];
            $result->partial = $variables['row_partial'];

            /** @var \Drupal\Core\Template\Attribute $attributes **/
            $attributes = $variables['rows'][$id]['attributes'];
            $attributes->addClass('list-item');

            /** @var DrupMediaImage $media */
            if ($media = $result->drupField->getDrupMedia('banner', 'image')) {
                if (!empty($result->imageStyle)) {
                    if ($mediaRendered = $media->renderMedias($result->imageStyle)) {
                        $result->media = current($mediaRendered);
                        $attributes->addClass('has-media');
                    }
                }
            }

            // Content
            $result->title = $result->_entity->getTitle();
            $result->url = $result->_entity->toUrl();
            $result->cta = $variables['row_cta'];
    
            // partial
            if (isset($result->partial)) {
                $attributes->addClass('l-'.$result->partial);
            }
            // Layout
            if (!empty($result->layout)) {
                $attributes->addClass('l-' . $result->layout);
            }

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
