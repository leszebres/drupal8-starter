<?php

use Drupal\Core\Render\Markup;

/**
 * @inheritdoc
 */
function drup_link_alter(&$variables) {
    $variables['text'] = Markup::create($variables['text']);
}
