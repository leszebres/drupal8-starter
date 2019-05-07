<?php

use Drupal\drup\Entity\Term;
use Drupal\drup\Entity\Node;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\drup\DrupHead;
use Drupal\node\NodeInterface;

/**
 * @inheritdoc
 */
function drup_site_entity_access(EntityInterface $entity, $operation, AccountInterface $account) {

}

/**
 * @inheritdoc
 */
function drup_site_entity_view_alter(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display) {
    if ($entity Instanceof NodeInterface && $build['#view_mode'] === 'full') {
        DrupHead::removeHeaderLinks($build);
    }
}

/**
 * @inheritdoc
 */
function drup_site_entity_view_mode_alter(&$view_mode, Drupal\Core\Entity\EntityInterface $entity, $context) {
    // Override view mode of some nodes
    if ($view_mode === 'full' && $entity->getEntityTypeId() === 'node') {
        /** @var \Drupal\drup\Entity\Node $entity */
        /*if ($entity->getType() === 'inspiration' && ($display = \Drupal::request()->query->get('display'))) {
            $view_mode = $display;
        }*/
    }
}

/**
 * @inheritdoc
 */
function drup_site_entity_type_build(array &$entity_types) {
    if (isset($entity_types['node'])) {
        $entity_types['node']->setClass(Node::class);
    }
    if (isset($entity_types['taxonomy_term'])) {
        $entity_types['taxonomy_term']->setClass(Term::class);
    }
}
