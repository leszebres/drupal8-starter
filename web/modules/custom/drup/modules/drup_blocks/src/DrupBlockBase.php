<?php

namespace Drupal\drup_blocks;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Block\BlockBase;

/**
 * Class DrupBlockBase
 * @package Drupal\drup_blocks
 */
abstract class DrupBlockBase extends BlockBase {
    
    /**
     * @return array|void
     */
    public function build() {}

    /**
     * @param array $parameters
     *
     * @return array
     */
    public function mergeBuildParameters($parameters = []) {
        $parameters = array_merge_recursive($parameters, []);
        return $parameters;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getCacheContexts() {
        return Cache::mergeContexts(parent::getCacheContexts(), []);
    }
    
    /**
     * {@inheritdoc}
     */
    public function getCacheTags() {
        return Cache::mergeTags(parent::getCacheTags(), DrupBlock::getDefaultCacheTags());
    }
}
