<?php

use Drupal\Core\Access\AccessResult;
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
    if ($operation === 'view') {
        // Terms
        if ($entity instanceof Term) {
            // Accès manuel : mettre le nom machine du vocabulaire
            $allowedVocabulariesId = [];

            // Accès automatique en fonction de la configuration du sitemap.xml
            if (\Drupal::moduleHandler()->moduleExists('simple_sitemap')) {
                /** @var \Drupal\simple_sitemap\Simplesitemap $generator */
                $generator = \Drupal::service('simple_sitemap.generator');
                $bundleSettings = (array) $generator->getBundleSettings($entity->getEntityTypeId());

                if (!empty($bundleSettings['index'])) {
                    $allowedVocabulariesId[] = $entity->getVocabularyId();
                }
            }

            return AccessResult::forbiddenIf(!in_array($entity->getVocabularyId(), $allowedVocabulariesId));
        }
    }
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
