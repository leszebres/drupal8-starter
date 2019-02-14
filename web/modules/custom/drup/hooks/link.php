<?php

use Drupal\Core\Render\Markup;

/**
 * Link text are HTML fiendly
 *
 * @param $variables
 */
function drup_link_alter(&$variables) {
    $variables['text'] = Markup::create($variables['text']);
}