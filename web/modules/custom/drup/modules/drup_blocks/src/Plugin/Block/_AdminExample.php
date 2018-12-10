<?php

namespace Drupal\drup_blocks\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

use Drupal\drup_blocks\DrupBlockAdmin;
use Drupal\drup_blocks\DrupBlockAdminBase;
use Drupal\drup\DrupSite;

/**
 * Provides a '_AdminExample' block.
 *
 * @Block(
 *  id = "admin_example",
 *  admin_label = @Translation("Admin Example"),
 * )
 */
class _AdminExample extends DrupBlockAdminBase {

    public $container = 'container';

    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state) {
        $form = parent::blockForm($form, $form_state);

        $form[$this->container] = [
            '#type' => 'fieldset',
            '#title' => t('Manage countries list'),
            '#tree' => true
        ];
        $form[$this->container]['list'] = [
            '#type' => 'textarea',
            '#title' => t('Countries list (one item per line)'),
            '#default_value' => !empty($this->drupConfiguration[$this->container]['list']) ? $this->drupConfiguration[$this->container]['list'] : null
        ];
    
        $form[$this->container]['contact'] = [
            '#type' => 'email',
            '#title' => t('Email'),
            '#default_value' => !empty($this->drupConfiguration[$this->container]['contact']) ? $this->drupConfiguration[$this->container]['contact'] : 'international@pileje.com'
        ];

        return $form;
    }
    
    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state) {
        $this->drupConfiguration[$this->container] = $form_state->getValue($this->container);

        parent::blockSubmit($form, $form_state);
    }
    
    /**
     * {@inheritdoc}
     */
    public function build() {
        parent::build();
        $items = [];
        $nbCountriesPerColumn = 5;

        if (!empty($this->drupValues[$this->container]['list'])) {
            $countries = explode("\r\n", $this->drupValues[$this->container]['list']);
            $items = array_chunk($countries, $nbCountriesPerColumn);
        }

        $build = $this->mergeBuildParameters([
            '#theme' => 'drup_blocks_admin_distributor_countries',
            '#items' => $items,
            '#contact_email' => isset($this->drupValues[$this->container]['contact']) ? $this->drupValues[$this->container]['contact'] : null,
        ]);
        
        return $build;
    }
}
