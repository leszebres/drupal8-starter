<?php

use Drupal\Core\Access\AccessResult;
use Drupal\drup\DrupCommon;

/**
 * Implements hook_node_access().
 */
function drup_node_access(NodeInterface $node, $op, AccountInterface $account) {
    // 403 status if node is not translated
    $isAllowed = DrupCommon::isNodeTranslated($node);
    $access = ($isAllowed === true) ? AccessResult::neutral() : AccessResult::forbidden();
    $access->addCacheableDependency($node);

    return $access;
}