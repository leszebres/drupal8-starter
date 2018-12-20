<?php

namespace Drupal\drup_settings\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\drup\DrupCommon;
use Drupal\drup_settings\DrupSettings;
use Drupal\file\Entity\File;

use Drupal\drup_settings\DrupSettingsVariables;

/**
 * Class DrupSettingsFrom.
 */
class DrupSettingsForm extends ConfigFormBase {

    /**
     * @var DrupSettings
     */
    public $drupSettings;

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'admin_form';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
        return ['system.site'];
    }

    /**
     *
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $this->drupSettings = new DrupSettings();

        $container = 'container';

        $form[$container] = [
            '#type' => 'horizontal_tabs',
            '#prefix' => '<div id="drup-settings-wrapper">',
            '#suffix' => '</div>',
            '#group_name' => 'drup_settings',
            '#entity_type' => 'drup_settings',
            '#bundle' => 'drup_settings'
        ];

        /**
         * MAIN SETTINGS
         */
        $form[$container]['main'] = [
            '#type' => 'details',
            '#title' => $this->t('Configuration du site'),
            '#collapsible' => true,
            '#collapsed' => true,
        ];

        $form[$container]['main']['site_information'] = [
            '#type' => 'details',
            '#title' => $this->t('Site details'),
            '#open' => true,
        ];
        $form[$container]['main']['site_information']['site_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Site name'),
            '#default_value' => $this->drupSettings->getValue('site_name'),
            '#required' => true,
        ];
        $form[$container]['main']['site_information']['site_slogan'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Slogan'),
            '#default_value' => $this->drupSettings->getValue('site_slogan'),
        ];

        /* SEO */
        $form[$container]['main']['site_seo'] = [
            '#type' => 'details',
            '#title' => $this->t('Référencement global'),
            '#open' => true
        ];
        $form[$container]['main']['site_seo']['site_logo_alt'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Attribut alt du logo'),
            '#default_value' => $this->drupSettings->getValue('site_logo_alt'),
        ];
        $form[$container]['main']['site_seo']['site_tag_manager'] = [
            '#type' => 'textfield',
            '#title' => $this->t('ID Google Tag Manager'),
            '#default_value' => $this->drupSettings->getValue('site_tag_manager'),
        ];


        $form[$container]['main']['home_seo'] = [
            '#type' => 'details',
            '#title' => $this->t('Référencement de la page d\'accueil'),
            '#open' => true,
        ];
        $form[$container]['main']['home_seo']['home_meta_title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Meta Title'),
            '#default_value' => $this->drupSettings->getValue('home_meta_title'),
        ];
        $form[$container]['main']['home_seo']['home_meta_desc'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Meta Description'),
            '#maxlength' => 250,
            '#default_value' => $this->drupSettings->getValue('home_meta_desc'),
        ];
        $form[$container]['main']['home_seo']['home_h1'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Titre principal H1'),
            '#default_value' => $this->drupSettings->getValue('home_h1'),
        ];


        $socialNetworks = \Drupal\drup\DrupSite::getSocialLinks();
        $form[$container]['main']['social_networks'] = [
            '#type' => 'details',
            '#title' => $this->t('URLs des réseaux sociaux'),
            '#open' => true,
        ];
        foreach ($socialNetworks as $socialNetworkID => $socialNetwork) {
            $form[$container]['main']['social_networks']['site_' . $socialNetworkID] = [
                '#type' => 'url',
                '#title' => $socialNetwork['title'],
                '#default_value' => $socialNetwork['url'],
            ];
        }

        /**
         * CONTACT
         */

        // Same info for every lang
        $this->drupSettings->setNeutralLang();

        $form[$container]['contact'] = [
            '#type' => 'details',
            '#title' => $this->t('Coordonnées'),
            '#collapsible' => true,
            '#collapsed' => true,
        ];

        $form[$container]['contact']['contact_infos'] = [
            '#type' => 'details',
            '#title' => t('Informations de contact'),
            '#open' => true,
        ];
        $form[$container]['contact']['contact_infos']['contact_infos_phone_number'] = [
            '#type' => 'textfield',
            '#title' => t('N° téléphone'),
            '#default_value' => $this->drupSettings->getValue('contact_infos_phone_number'),
        ];

        // Back to current lang
        $this->drupSettings->setLang();

        /**
         * .....
         */

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        foreach ($form_state->getValues() as $fieldID => $fieldValue) {
            if (strpos($fieldID, 'contact_') === 0) {
                $this->drupSettings->setNeutralLang();
            } else {
                $this->drupSettings->setLang();
            }
            $this->drupSettings->set($fieldID, $fieldValue);

            if (!empty($fieldValue) && (strpos($fieldID, 'image') !== false || strpos($fieldID, 'file') !== false)) {
                DrupCommon::setFilePermanent($fieldValue);
            }
        }

        $this->drupSettings->setLang();
        $this->drupSettings->save();

        parent::submitForm($form, $form_state);
    }
}
