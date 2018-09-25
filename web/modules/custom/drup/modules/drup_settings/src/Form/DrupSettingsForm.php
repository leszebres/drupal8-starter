<?php

namespace Drupal\drup_settings\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\file\Entity\File;

use Drupal\drup_settings\DrupSettingsVariables;

/**
 * Class DrupSettingsFrom.
 */
class DrupSettingsForm extends ConfigFormBase {

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
        $drupSettingsVariables = \Drupal::service('drup_settings.variables');

        /*
         * @info All form field ids must be prefixed by site_
         */
        $form['site_information'] = array(
            '#type' => 'details',
            '#title' => t('Site details'),
            '#open' => TRUE,
        );
        $form['site_information']['site_name'] = [
            '#type' => 'textfield',
            '#title' => t('Site name'),
            '#default_value' => $drupSettingsVariables->getValue('site_name'),
            '#required' => TRUE
        ];
        $form['site_information']['site_slogan'] = [
            '#type' => 'textfield',
            '#title' => t('Slogan'),
            '#default_value' => $drupSettingsVariables->getValue('site_slogan')
        ];
        
    
        $form['subcountry_information'] = [
            '#type' => 'fieldset',
            '#title' => t('Fonctionnalités')
        ];
        $form['subcountry_information']['site_tag_manager'] = [
            '#type' => 'textfield',
            '#title' => t('ID Google Tag Manager'),
            '#default_value' => $drupSettingsVariables->getValue('site_tag_manager')
        ];
    
        $socialNetworks = \Drupal\drup\DrupSite::getSocialLinks();
        $form['social_networks'] = [
            '#type' => 'fieldset',
            '#title' => t('Social networks'),
            '#description' => t('Links present in site footer')
        ];
        foreach ($socialNetworks as $socialNetworkID => $socialNetwork) {
            $form['social_networks']['site_'.$socialNetworkID] = [
                '#type' => 'url',
                '#title' => $socialNetwork['title'],
                '#default_value' => $socialNetwork['url']
            ];
        }
        
        $form['contact_infos'] = [
            '#type' => 'fieldset',
            '#title' => t('Informations de contact')
        ];
        $form['contact_infos']['contact_infos_phone_number'] = [
            '#type' => 'textfield',
            '#title' => t('N° téléphone'),
            '#default_value' => $drupSettingsVariables->getValue('contact_infos_phone_number')
        ];
        
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $drupSettingsVariables = new DrupSettingsVariables();
        
        foreach ($form_state->getValues() as $fieldID => $fieldValue) {
            $drupSettingsVariables->set($fieldID, $fieldValue);
            
            if (strpos($fieldID, 'image') !== FALSE && !empty($fieldValue)) {
                if ($file = File::load($fieldValue[0])) {
                    $file->setPermanent();
                    $file->save();
                }
            }
        }
        $drupSettingsVariables->save();

        parent::submitForm($form, $form_state);
    }
}
