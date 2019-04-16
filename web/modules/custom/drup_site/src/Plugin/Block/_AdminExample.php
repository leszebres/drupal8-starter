<?php

namespace Drupal\drup_blocks\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\drup\Block\DrupBlockAdminBase;
use Drupal\drup\Media\DrupFile;

/**
 * Provides a '_AdminExample' block.
 *
 * @Block(
 *  id = "admin_example",
 *  admin_label = @Translation("Admin Example"),
 * )
 */
class _AdminExample extends DrupBlockAdminBase {

    /**
     * {@inheritdoc}
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
    }

    /**
     * {@inheritdoc}
     */
    public function setAjaxRow(&$rowContainer, $rowValues) {
        if (!empty($rowValues['title'])) {
            $rowContainer['#title'] = $rowValues['title'];
        }
        $rowContainer['title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Title'),
            '#required' => true,
            '#default_value' => !empty($rowValues['title']) ? $rowValues['title'] : null
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state) {
        $form = parent::blockForm($form, $form_state);

        $form['title'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Title'),
            '#rows' => 2,
            '#default_value' => !empty($this->configValues['title']) ? $this->configValues['title'] : null
        ];

        $this->ajaxMaxRows = 5;
        $this->buildAjaxContainer($form, $form_state);

        return $form;
    }
    
    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state) {
        $this->configValues['title'] = &$form_state->getValue('title');
        $this->configValues[$this->ajaxContainer] = &$form_state->getValue($this->ajaxContainer);

        if (!empty($this->configValues[$this->ajaxContainer])) {
            foreach ($this->configValues[$this->ajaxContainer] as $index => $formItem) {
                if (!empty($formItem['logo'])) {
                    DrupFile::setPermanent($formItem['logo']);
                }
            }
        }

        parent::blockSubmit($form, $form_state);
    }
    
    /**
     * {@inheritdoc}
     */
    public function build() {
        parent::build();

        $items = [];
        if (!empty($this->configValues[$this->ajaxContainer])) {
            foreach ($this->configValues[$this->ajaxContainer] as $index => $formItem) {
                if (!empty($formItem['title'])) {
                    $items[] = $formItem['title'];
                }
            }
        }

        $build = $this->mergeBuildParameters([
            '#theme' => 'drup_blocks_admin_admin_example',
            '#title' => $this->configValues['title'],
            '#items' => $items
        ]);

        return $build;
    }
}
