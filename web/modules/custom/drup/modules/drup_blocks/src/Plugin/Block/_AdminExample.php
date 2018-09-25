<?php

namespace Drupal\drup_blocks\Plugin\Block;

use Drupal\Core\Form\FormStateInterface;
use Drupal\node\Entity\Node;

use Drupal\drup_blocks\DrupBlockAdmin;
use Drupal\drup_blocks\DrupBlockAdminBase;
use Drupal\drup\DrupSite;

/**
 * Provides a 'Distributor Countries' block.
 *
 * @Block(
 *  id = "distributor_countries",
 *  admin_label = @Translation("Distributor Countries"),
 * )
 */
class AdminExample extends DrupBlockAdminBase {

    protected $container = 'example';

    /**
     * {@inheritdoc}
     */
    public function blockForm($form, FormStateInterface $form_state) {
        $form[$this->container] = [
            '#type' => 'fieldset',
            '#title' => t('Manage countries list'),
            '#tree' => true
        ];
        $form[$this->container]['list'] = [
            '#type' => 'textarea',
            '#title' => t('Countries list (one item per line)'),
            '#default_value' => (!empty($this->drupConfiguration[$this->container]['list'])) ? $this->drupConfiguration[$this->container]['list'] : null
        ];
    
        $form[$this->container]['contact'] = [
            '#type' => 'email',
            '#title' => t('Email'),
            '#default_value' => (!empty($this->drupConfiguration[$this->container]['contact'])) ? $this->drupConfiguration[$this->container]['contact'] : 'international@pileje.com'
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

        $build = [
            '#theme' => 'drup_blocks_admin_distributor_countries',
            '#items' => $items,
            '#contact_email' => (isset($this->drupValues[$this->container]['contact'])) ? $this->drupValues[$this->container]['contact'] : null,
            '#admin_url' => DrupBlockAdmin::getAdminConfigUrl($this->configuration['id'])
        ];
        
        return $build;
    }
}
