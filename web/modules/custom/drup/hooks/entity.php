<?php

use Drupal\drup\DrupCommon;

/**
 * Implements hook_entity_access().
 * @param \Drupal\Core\Entity\EntityInterface $entity
 * @param $operation
 * @param \Drupal\Core\Session\AccountInterface $account
 */
function drup_entity_access(\Drupal\Core\Entity\EntityInterface $entity, $operation, \Drupal\Core\Session\AccountInterface $account) {

}

/**
 * Implements hook_entity_view_alter().
 * @param array $build
 * @param \Drupal\Core\Entity\EntityInterface $entity
 * @param \Drupal\Core\Entity\Display\EntityViewDisplayInterface $display
 */
function drup_entity_view_alter(array &$build, Drupal\Core\Entity\EntityInterface $entity, \Drupal\Core\Entity\Display\EntityViewDisplayInterface $display) {
    if ($entity Instanceof NodeInterface && $build['#view_mode'] === 'full') {
        DrupCommon::removeHeaderLinks($build);
    }
}