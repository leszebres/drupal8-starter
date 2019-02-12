<?php

use Drupal\Core\Template\Attribute;
use Drupal\Core\Url;
use Drupal\Component\Utility\Html;

use Drupal\drup\DrupEntityImage;

/**
 * @param array $variables
 */
function drup_theme_preprocess_paragraph(array &$variables) {
    $paragraph = $variables['paragraph'];

    switch ($paragraph->getType()) {
        // Code html
        case 'html':
            $variables['content'] = Drupal\Core\Render\Markup::create($paragraph->get('field_html_code')->value);
            break;

        case 'text_image':
            $variables['image_position'] = $paragraph->get('field_image_position')->value;
            $variables['attributes']['class'][] = 'l-' . $variables['image_position'];
            break;

        default:
            break;
    }
}
