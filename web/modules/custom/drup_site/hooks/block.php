<?php

use Drupal\Core\Access\AccessResult;
use Drupal\drup\Helper\DrupRequest;


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
 *
 * @return \Drupal\Core\Access\AccessResult
 */
function drup_site_block_access(\Drupal\block\Entity\Block $block, $operation, \Drupal\Core\Session\AccountInterface $account) {
    /** @var \Drupal\drup_router\DrupRouter $drupRouter */
    $drupRouter = \Drupal::service('drup_router');
    $drupRouteName = \Drupal::service('drup_router')->getName();

    /** @var \Drupal\drup\DrupPageEntity $drupPageEntity */
    $drupPageEntity = \Drupal::service('drup_page_entity');

    $isFront = DrupRequest::isFront();
    $args = DrupRequest::getArgs();

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
//                return AccessResult::forbiddenIf($drupRouteName !== 'news')->addCacheableDependency($block);
//                break;

            /**
             * Content after
             */
//            case 'push_newsletter_block':
//                return AccessResult::forbiddenIf($drupRouteName === 'newsletter')->addCacheableDependency($block);
//                break;
            
        }
    }
}
