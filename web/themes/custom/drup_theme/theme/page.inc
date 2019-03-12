<?php

use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\node\Entity\Node;

use Drupal\drup_settings\DrupSettings;
use Drupal\drup\DrupEntityImage;
use Drupal\drup\Entity\DrupEntityField;
use Drupal\drup\DrupCommon;
use Drupal\drup\DrupSite;
use Drupal\drup_site\CookieNotice;

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
    $drupSettings = new DrupSettings();
    $pageEntity = DrupCommon::getPageEntity();
    
    $drupRouter = \Drupal::service('drup_router.router');
    $drupRouteName = $drupRouter->getName();
    $systemRouteName = \Drupal::routeMatch()->getRouteName();
    $isFront = \Drupal::service('path.matcher')->isFrontPage();

    $variables['cookies_data'] = CookieNotice\Config::get();
    
    $variables['site_name'] = $drupSettings->getValue('site_name');
    $variables['site_slogan'] = $drupSettings->getValue('site_slogan');

    /**
     * Load librariesw
     */
    $variables['page']['#cache']['contexts'][] = 'url.path'; // sets the cacheability metadata

    if ($isFront) {
    
    } else {
        if ($drupRouteName === 'contact') {
            $variables['#attached']['library'][] = 'drup_theme/theme-contact';
        }
    }
    
    
    /**
     * Node fields
     */
    $node = null;
    if (isset($variables['node'])) {
        $node = $variables['node'];
    }
    if (is_string($node)) {
        $node = Node::load($node);
    }

    if ($node instanceof Node) {
        $nodeType = $node->getType();
        $drupField = new DrupEntityField($node);

        // Redirect node to their DrupRoute
        $nodesRedirection = [
            'factory' => '<front>'
        ];
        if (isset($nodesRedirection[$nodeType])) {
            $path = ($nodesRedirection[$nodeType] === '<front>') ? Url::fromRoute($nodesRedirection[$nodeType])->toString() : $drupRouter->getPath($nodesRedirection[$nodeType]);
            $redirection = new RedirectResponse($path);
            $redirection->send();
            return;
        }
        
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
//    if (($node = \Drupal::routeMatch()->getParameter('node')) && $node instanceof Node) {
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
    $entity = DrupCommon::getPageEntity(true);
    $drupRouter  = \Drupal::service('drup_router.router');

    $variables['title_attributes'] = new Attribute([
        'class' => ['content-title']
    ]);
    $variables['tag'] = 'h1';
}
