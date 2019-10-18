<?php

use Drupal\Core\Access\AccessResult;
use Drupal\Core\Session\AccountInterface;
use Drupal\drup\Entity\ContentEntityBase;
use Drupal\drup\Helper\DrupRequest;
use Drupal\node\NodeInterface;

/**
 * {@inheritdoc}
 */
function drup_site_node_access(NodeInterface $node, $op, AccountInterface $account) {
    if (!DrupRequest::isAdminRoute()) {
        // 403 status if node is not translated
        $isAllowed = ContentEntityBase::isAllowed($node);
        $access = $isAllowed ? AccessResult::neutral() : AccessResult::forbidden();
        $access->addCacheableDependency($node);

        return $access;
    }

    return AccessResult::neutral();
}

/**
 * A l'enregistrement d'un nouveau type de contenu, on force les champs metatags à afficher seulement "basic"
 *
 * @param object $nodeType \Drupal\node\Entity\NodeType
 */
function drup_site_node_type_insert($nodeType) {
    if (\Drupal::moduleHandler()->moduleExists('metatag')) {
        /** @var \Drupal\Core\Config\Config $config */
        $config = \Drupal::service('config.factory')->getEditable('metatag.settings');
        $entityTypeGroups = $config->get('entity_type_groups');

        $entityTypeGroups['node'][$nodeType->id()] = [
            'basic' => 'basic'
        ];

        $config->set('entity_type_groups', $entityTypeGroups);
        $config->save();
    }
}
