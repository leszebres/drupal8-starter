<?php

namespace Drupal\drup_blocks;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DrupBlockAdminBase
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
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state) {
        parent::blockForm($form, $form_state);
    }
    
    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state) {

    }
    
    /**
     * @return array|void
     */
    public function build() {

    }
    
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
