<?php

use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;
use Drupal\drup\DrupPageEntity;
use Drupal\drup\DrupSEO;
use Drupal\drup\Entity\DrupField;
use Drupal\drup\Entity\Node;
use Drupal\drup\Helper\DrupRequest;
use Drupal\drup_settings\DrupSettings;
use Drupal\drup_site\CookieNotice;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Implements hook_preprocess_page() for page.html.twig.
 */
function drup_theme_preprocess_page(array &$variables) {
    $drupSettings = new DrupSettings();
    $drupPageEntity = DrupPageEntity::loadEntity();
    $drupRouter = \Drupal::service('drup_router');
    $drupRouteName = $drupRouter->getName();
    $systemRouteName = DrupRequest::getRouteName();
    $isFront = DrupRequest::isFront();

    $variables['cookie_notice'] = CookieNotice\Config::get();
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
        $drupField = $node->drupField();

        // Redirect node to their DrupRoute
        $nodesRedirection =  [
            'factory' => '<front>'
        ];
        if (isset($nodesRedirection[$nodeType])) {
            $path = ($nodesRedirection[$nodeType] === '<front>') ? Url::fromRoute($nodesRedirection[$nodeType])->toString() : $drupRouter->getPath($nodesRedirection[$nodeType]);
            $redirection = new RedirectResponse($path);
            $redirection->send();
            exit;
        }
        
    } elseif (\Drupal::request()->attributes->get('_route') === 'system.404') {
        $variables['page']['content'] = DrupRequest::get404Content();
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
    $drupPageEntity = DrupPageEntity::loadEntity();
    $drupRouter = \Drupal::service('drup_router');

    $variables['title_attributes'] = new Attribute([
        'class' => ['content-title']
    ]);
    $variables['tag'] = 'h1';

//    if ($entity = $drupPageEntity->getEntity()) {
//        $drupField = new DrupField($drupPageEntity->getEntity());
//
//    }
}

/**
 * Implements hook_preprocess_pager().
 */
function drup_theme_preprocess_pager(&$pager) {
    DrupSEO::pagerHandler($pager);
}
