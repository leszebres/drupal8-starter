<?php

use Drupal\Core\Render\Markup;

/**
 * {@inheritdoc}
 */
function drup_site_link_alter(&$variables) {
    $variables['text'] = Markup::create($variables['text']);

    if ($variables['url']->isExternal()) {
        $variables['options']['attributes']['target'] = '_blank';
    }
}
