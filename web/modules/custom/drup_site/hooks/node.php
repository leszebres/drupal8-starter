<?php

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\drup\DrupCommon;
use Drupal\drup\DrupMenu;
use Drupal\drup\Entity\Node;
use Drupal\node\NodeInterface;

/**
 * @inheritdoc
 */
function drup_site_node_access(NodeInterface $node, $op, AccountInterface $account) {
    if (!\Drupal::service('router.admin_context')->isAdminRoute()) {
        // 403 status if node is not translated
        $languageId = \Drupal::languageManager()->getCurrentLanguage()->getId();

        $isAllowed = $node->hasTranslation($languageId);
        $access = $isAllowed ? AccessResult::neutral() : AccessResult::forbidden();
        $access->addCacheableDependency($node);

        return $access;
    }
}
