<?php

use Drupal\Core\Template\Attribute;
use Drupal\Component\Utility\Html;
use Drupal\drup_settings\DrupSettings;

use Drupal\drup\DrupCommon;

/**
 * @param array $variables
 */
function drup_theme_preprocess_html(array &$variables) {
    $drupSettings = new DrupSettings();
    $drupRouter = \Drupal::service('drup_router.router');

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
        $pageEntity = DrupCommon::getPageEntity();

        if ($pageEntity->type && $pageEntity->bundle) {
            $variables['attributes']->addClass([
                'entity-type--' . Html::cleanCssIdentifier($pageEntity->type),
                'entity-bundle--' . Html::cleanCssIdentifier($pageEntity->bundle)
            ]);
        }
    }
    if (isset($variables['node_type'])) {
        $variables['attributes']->addClass('node-type--' . Html::cleanCssIdentifier($variables['node_type']));
    }
    if ($routeName = $drupRouter->getName()) {
        $variables['#attached']['drupalSettings']['currentRoute'] = $routeName;
        $variables['attributes']->addClass('route--' . Html::cleanCssIdentifier($routeName));
    }
    
    // User role @see module drup_admin_toolbar
    
    $variables['google_tag'] = $drupSettings->getValue('site_tag_manager');;

    DrupCommon::addFavicons($variables, ['color_mask_icon' => '#5bbad5', 'color_msapplication' => '#da532c', 'color_theme' => '#ffffff'], 1);
}
