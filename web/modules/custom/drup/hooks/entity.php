<?php

use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;
use Drupal\drup\DrupCommon;
use Drupal\node\NodeInterface;

/**
 * @inheritdoc
 */
function drup_entity_access(EntityInterface $entity, $operation, AccountInterface $account) {

}

/**
 * @inheritdoc
 */
function drup_entity_view_alter(array &$build, EntityInterface $entity, EntityViewDisplayInterface $display) {
    if ($entity Instanceof NodeInterface && $build['#view_mode'] === 'full') {
        DrupCommon::removeHeaderLinks($build);
    }
}
