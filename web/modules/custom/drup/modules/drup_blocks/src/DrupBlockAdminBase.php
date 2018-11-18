<?php

namespace Drupal\drup_blocks;

use Drupal\Core\Cache\Cache;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Class DrupBlockAdminBase
 * @package Drupal\drup_blocks
 */
abstract class DrupBlockAdminBase extends BlockBase {
    
    public $drupConfiguration;
    public $drupContext;
    public $drupContextKey;
    public $drupValues;
    
    /**
     * {@inheritdoc}
     */
    public function defaultConfiguration() {
        if (isset($this->configuration['id'])) {
            $this->drupContext = DrupBlockAdmin::getContext('admin');
            $this->drupContextKey = DrupBlockAdmin::formatContextedKey($this->configuration['id'], $this->drupContext);
            $this->drupConfiguration = DrupBlockAdmin::getContextedValues($this->configuration['id'], 'admin');
        }
        return parent::defaultConfiguration();
    }
    
    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state) {
        parent::blockForm($form, $form_state);
        
        if (empty($this->drupContext)) {
            \Drupal::messenger()->addMessage($this->t('Missing context'), 'error');
            return $form;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state) {
        DrupBlockAdmin::setValues($this->drupContextKey, $this->drupConfiguration);
    }
    
    /**
     * @return array|void
     */
    public function build() {
        $this->drupValues = DrupBlockAdmin::getContextedValues($this->configuration['id'], $this->drupContext);
    }

    /**
     * @param array $parameters
     *
     * @return array
     */
    public function mergeBuildParameters($parameters = []) {
        $parameters = array_merge_recursive($parameters, [
            '#admin_url' => DrupBlockAdmin::getAdminConfigUrl($this->configuration['id'])
        ]);
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
