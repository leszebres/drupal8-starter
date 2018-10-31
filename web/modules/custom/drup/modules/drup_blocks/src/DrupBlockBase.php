<?php

namespace Drupal\drup_blocks;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DrupBlockBase
 * @package Drupal\drup_blocks
 */
abstract class DrupBlockBase extends BlockBase {
    
    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration() {
        return parent::defaultConfiguration();
    }
    
    /**
     * @return array|void
     */
    public function build() {}
    
    /**
     * @param array $parameters
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
