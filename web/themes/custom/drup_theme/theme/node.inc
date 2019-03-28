<?php

use Drupal\drup\Entity\Node;

/**
 * @param array $suggestions
 * @param array $variables
 */
function drup_theme_theme_suggestions_node_alter(array &$suggestions, array $variables) {
  /*$node = $variables['elements']['#node'];

  if ($variables['elements']['#view_mode'] == "full") {

  }*/
}


/**
 * @param array $variables
 */
function drup_theme_preprocess_node(array &$variables) {
    $node = Node::load($variables['node']->id());
    if (!$node instanceof Node) {
        return;
    }

    $nodeType = $node->getType();
    $drupField = $node->drupField();

    $variables['fields'] = [];
}
