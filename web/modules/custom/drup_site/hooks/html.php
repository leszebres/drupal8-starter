<?php

use Drupal\Component\Utility\Html;
use Drupal\Core\Template\Attribute;

/**
 * Implements hook_preprocess_html().
 */
function drup_site_preprocess_html(&$variables) {
    // Body classes
    if (!($variables['attributes'] instanceof Attribute)) {
        $variables['attributes'] = new Attribute();
    }

    foreach ($variables['user']->getRoles() as $role) {
        $variables['attributes']->addClass('role--' . Html::cleanCssIdentifier($role));
    }
}