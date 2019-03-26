<?php

use Drupal\Core\Template\Attribute;
use Drupal\Component\Utility\Html;
use Drupal\drup\DrupHead;
use Drupal\drup\DrupPageEntity;
use Drupal\drup\DrupSEO;
use Drupal\drup\Helper\DrupRequest;
use Drupal\drup_settings\DrupSettings;

/**
 * @inheritdoc
 */
function drup_theme_preprocess_html(array &$variables) {
    $drupSettings = new DrupSettings();
    $drupRouter = \Drupal::service('drup_router.router');
    $systemRouteName = DrupRequest::getRouteName();

    // JS variables
    $variables['#attached']['drupalSettings']['pathToTheme'] = '/' . drupal_get_path('theme', 'drup_theme');

    // Body classes
    if (!($variables['attributes'] instanceof Attribute)) {
        $variables['attributes'] = new Attribute();
    }
    if ($variables['logged_in']) {
        $variables['attributes']->addClass('is-logged-in');
    }
    if ($variables['root_path'] === false) {
        $variables['attributes']->addClass('is-front');

    } else {
        $drupPageEntity = DrupPageEntity::loadEntity();

        if (($entityType = $drupPageEntity->getEntityType()) && ($bundle = $drupPageEntity->getBundle())) {
            $variables['attributes']->addClass([
                'entity-type--' . Html::cleanCssIdentifier($entityType),
                'entity-bundle--' . Html::cleanCssIdentifier($bundle)
            ]);
        }
    }
    if (isset($variables['node_type'])) {
        $variables['attributes']->addClass('node-type--' . Html::cleanCssIdentifier($variables['node_type']));

        // Site title
        DrupSEO::addSiteTitle($variables['head_title']['title']);
    }
    if ($routeName = $drupRouter->getName()) {
        $variables['#attached']['drupalSettings']['currentRoute'] = $routeName;
        $variables['attributes']->addClass('route--' . Html::cleanCssIdentifier($routeName));
    }
    if ($systemRouteName) {
        $variables['attributes']->addClass('route--' . Html::cleanCssIdentifier(str_replace('.', '-', $systemRouteName)));
    }
    
    // User role @see module drup_admin_toolbar
    
    $variables['google_tag'] = $drupSettings->getValue('site_tag_manager');;

    DrupHead::addFavicons($variables, [
        'color_mask_icon' => '#5bbad5', 'color_msapplication' => '#da532c', 'color_theme' => '#ffffff'
    ], 1);
}
