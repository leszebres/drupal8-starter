<?php

use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;
use Drupal\Component\Utility\Html;

use Drupal\drup\DrupEntityImage;

/**
 * Preprocess des contenus Paragraphs
 *
 * @param array $variables
 */
function drup_theme_preprocess_paragraph(array &$variables) {
    $paragraph = $variables['paragraph'];

    switch ($paragraph->getType()) {
        default:
            break;
    }
}
