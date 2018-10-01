<?php

use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;
use Drupal\taxonomy\Entity\Term;
use Drupal\drup\DrupEntityImage;
use Drupal\drup\DrupEntityField;
use Drupal\drup\DrupCommon;
use Drupal\drup\DrupSite;
use Drupal\drup\CookieNotice;

/**
 * Implements hook_page_attachments_alter().
 */
function drup_theme_page_attachments_alter(array &$page) {
  // Tell IE to use latest rendering engine (not to use compatibility mode).
  /*$ie_edge = [
    '#type' => 'html_tag',
    '#tag' => 'meta',
    '#attributes' => [
    'http-equiv' => 'X-UA-Compatible',
    'content' => 'IE=edge',
    ],
  ];
  $page['#attached']['html_head'][] = [$ie_edge, 'ie_edge'];*/
}

/**
 * Implements hook_preprocess_page() for page.html.twig.
 */
function drup_theme_preprocess_page(array &$variables) {
    $drupSettings = \Drupal::service('drup_settings.variables');
    $pageEntity = DrupCommon::getPageEntity();
    
    $drupRouter = \Drupal::service('drup_router.router');
    $routeName = $drupRouter->getName();
    
    $cookiesDatas = CookieNotice\Config::get();
    $variables['cookies_datas'] = $cookiesDatas;
    
    $variables['site_name'] = $drupSettings->getValue('site_name');
    $variables['site_slogan'] = $drupSettings->getValue('site_slogan');

    /**
     * Load libraries
     */
    $variables['page']['#cache']['contexts'][] = 'url.path'; // sets the cacheability metadata
    if (\Drupal::service('path.matcher')->isFrontPage()) {
    
    } else {
        if ($routeName === 'contact') {
            $variables['#attached']['library'][] = 'drup_theme/theme-contact';
        }
    }
    
    
    /**
     * Node fields
     */
    if (isset($variables['node'])) {
        $node = $variables['node'];
        $nodeType = $node->getType();
        $drupField = new DrupEntityField($node);
    
    } elseif (\Drupal::request()->attributes->get('_route') === 'system.404') {
        $variables['page']['content'] = DrupSite::get404Content();
    }
}

/**
 * Implements hook_theme_suggestions_page_alter().
 */
function drup_theme_theme_suggestions_page_alter(array &$suggestions, array $variables) {
//    $pageSecondaryNodeTypes = ['healthreview_article'];
//
//    if ($node = \Drupal::routeMatch()->getParameter('node')) {
//        $nodeType = $node->getType();
//
//        if (in_array($nodeType, $pageSecondaryNodeTypes)) {
//            $suggestions[] = 'page__secondary';
//        }
//    }
}
    
    /**
 * Implements hook_preprocess_page_title().
 * @param array $variables
 */
function drup_theme_preprocess_page_title(array &$variables) {
    $entity = \Drupal\drup\DrupCommon::getPageEntity(true);
    
    $variables['title_attributes'] = new Attribute([
        'class' => ['content-title']
    ]);
    
    if ($entity->type === 'taxonomy_term') {
        $variables['title'] = strip_tags($variables['title']->__toString(), 'span');
    }
}
