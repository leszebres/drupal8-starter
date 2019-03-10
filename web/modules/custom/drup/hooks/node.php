<?php

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\drup\DrupCommon;
use Drupal\node\NodeInterface;

/**
 * @inheritdoc
 */
function drup_node_access(NodeInterface $node, $op, AccountInterface $account) {
    if (!\Drupal::service('router.admin_context')->isAdminRoute()) {
        // 403 status if node is not translated
        $isAllowed = DrupCommon::isNodeTranslated($node);
        $access = ($isAllowed === true) ? AccessResult::neutral() : AccessResult::forbidden();
        $access->addCacheableDependency($node);

        return $access;
    }
}
