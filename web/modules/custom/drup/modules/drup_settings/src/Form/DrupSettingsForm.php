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
        $drupSettings = \Drupal::service('drup_settings.variables');
        
        $form['site_information'] = array(
            '#type' => 'details',
            '#title' => $this->t('Site details'),
            '#open' => TRUE,
        );
        $form['site_information']['site_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Site name'),
            '#default_value' => $drupSettings->getValue('site_name'),
            '#required' => TRUE
        ];
        $form['site_information']['site_slogan'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Slogan'),
            '#default_value' => $drupSettings->getValue('site_slogan')
        ];
    
        $form['site_seo'] = array(
            '#type' => 'details',
            '#title' => $this->t('Référencement global'),
            '#open' => TRUE,
        );
        $form['site_seo']['site_logo_alt'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Attribut alt du logo'),
            '#default_value' => $drupSettings->getValue('site_logo_alt')
        ];
        $form['site_seo']['site_tag_manager'] = [
            '#type' => 'textfield',
            '#title' => $this->t('ID Google Tag Manager'),
            '#default_value' => $drupSettings->getValue('site_tag_manager')
        ];
        
    
        $form['home_seo'] = array(
            '#type' => 'details',
            '#title' => $this->t('Référencement de la page d\'accueil'),
            '#open' => TRUE,
        );
        $form['home_seo']['home_meta_title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Meta Title'),
            '#default_value' => $drupSettings->getValue('home_meta_title')
        ];
        $form['home_seo']['home_meta_desc'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Meta Description'),
            '#maxlength' => 250,
            '#default_value' => $drupSettings->getValue('home_meta_desc')
        ];
        $form['home_seo']['home_h1'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Titre principal H1'),
            '#default_value' => $drupSettings->getValue('home_h1')
        ];
        
    
        $socialNetworks = \Drupal\drup\DrupSite::getSocialLinks();
        $form['social_networks'] = [
            '#type' => 'details',
            '#title' => $this->t('URLs des réseaux sociaux'),
            '#open' => true
        ];
        foreach ($socialNetworks as $socialNetworkID => $socialNetwork) {
            $form['social_networks']['site_'.$socialNetworkID] = [
                '#type' => 'url',
                '#title' => $socialNetwork['title'],
                '#default_value' => $socialNetwork['url']
            ];
        }
        
        $form['contact_infos'] = [
            '#type' => 'details',
            '#title' => t('Informations de contact'),
            '#open' => true
        ];
        $form['contact_infos']['contact_infos_phone_number'] = [
            '#type' => 'textfield',
            '#title' => t('N° téléphone'),
            '#default_value' => $drupSettings->getValue('contact_infos_phone_number')
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
