<?php

/**
 * @file
 * Contains drup_blocks.module.
 */

use Drupal\drup_blocks\DrupBlock;
use Drupal\Core\Access\AccessResult;

    
/**
 * Hook build alter
 *
 * @param array $build
 * @param \Drupal\Core\Block\BlockPluginInterface $block
 */
function drup_site_block_build_alter(array &$build, \Drupal\Core\Block\BlockPluginInterface $block) {
    // Désactivation du cache pour certains blockId
    $disableCacheBlockId = [
        'system_branding_block'
    ];
    
    if (in_array($block->getPluginId(), $disableCacheBlockId)) {
        $build['#cache']['max-age'] = 0;
    }
}

/**
 * @param \Drupal\block\Entity\Block $block
 * @param $operation
 * @param \Drupal\Core\Session\AccountInterface $account
 */
function drup_site_block_access(\Drupal\block\Entity\Block $block, $operation, \Drupal\Core\Session\AccountInterface $account) {
    $currentRoute = \Drupal::service('drup_router.router')->getName();
    $entity = \Drupal\drup\DrupCommon::getPageEntity();
    $isFront = \Drupal::service('path.matcher')->isFrontPage();
    $args = \Drupal::routeMatch()->getParameters()->all();

    if ($operation === 'view') {
        switch ($block->id()) {
            /*
            * Content before
            */
            
            /*
             * Content
             */

            /**
             * Content views
             */
//            case 'views_block:news-list_all':
//                return AccessResult::forbiddenIf($currentRoute !== 'news')->addCacheableDependency($block);
//                break;

            /**
             * Content after
             */
//            case 'push_newsletter_block':
//                return AccessResult::forbiddenIf($currentRoute === 'newsletter')->addCacheableDependency($block);
//                break;
            
        }
    }
}
