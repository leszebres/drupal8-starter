<?php

use Drupal\Core\Render\Markup;
use Drupal\drup\Entity\DrupField;

/**
 * @param array $variables
 */
function drup_theme_preprocess_paragraph(array &$variables) {
    $paragraph = $variables['paragraph'];
    $drupField = new DrupField($paragraph);

    $variables['attributes']['data-id'] = $paragraph->id();

    switch ($paragraph->getType()) {
        // Code html
        case 'html':
            $variables['content'] = Markup::create($drupField->getValue('html_code', 'value'));
            break;

        // Text + image
        case 'text_image':
            if ($variables['image_position'] = $drupField->getValue('layout_image', 'value')) {
                $variables['attributes']['class'][] = 'l-' . $variables['image_position'];
            }
            break;

        default:
            break;
    }
}
