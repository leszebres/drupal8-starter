<?php

use Drupal\drup\Entity\Node;

/**
 * @param array $suggestions
 * @param array $variables
 */
function drup_theme_theme_suggestions_node_alter(array &$suggestions, array $variables) {
}


/**
 * @param array $variables
 */
function drup_theme_preprocess_node(array &$variables) {
    $node = $variables['node'];
    if (!$variables['node'] instanceof Node) {
        return;
    }

    $nodeType = $node->getType();
    $drupField = $node->drupField();

    $variables['fields'] = [];
}
