<?php

namespace Drupal\drup_site\Form;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Locale\CountryManager;
use Drupal\drup\Media\DrupFile;
use Drupal\drup_settings\Form\DrupSettingsForm;
use Drupal\drup_social_links\DrupSocialLinks;

/**
 * Class DrupSiteSettingsForm
 *
 * @package Drupal\drup_site\Form
 */
class DrupSiteSettingsForm extends DrupSettingsForm {

    /**
     * Chaque form item doit :
     * - préciser une clé '#drup_context' => 'und'|null (null pour la langue courante)
     * - ne pas inclure de '#default_value
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form = parent::buildForm($form, $form_state);

        /**
         * CONTACT
         */
        $form[$this->formContainer]['contact'] = [
            '#type' => 'details',
            '#title' => $this->t('Coordonnées'),
            '#collapsible' => true,
            '#collapsed' => true,
        ];

        $form[$this->formContainer]['contact']['contact_infos'] = [
            '#type' => 'details',
            '#title' => $this->t('Informations de contact'),
            '#open' => true,
        ];
        $form[$this->formContainer]['contact']['contact_infos']['contact_infos_phone_number'] = [
            '#type' => 'textfield',
            '#title' => $this->t('N° téléphone'),
            '#drup_context' => 'und'
        ];

        $form[$this->formContainer]['contact']['contact_infos']['contact_infos_address'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Address'),
            '#rows' => 2,
            '#drup_context' => 'und'
        ];
        $form[$this->formContainer]['contact']['contact_infos']['contact_infos_zipcode'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Zip code'),
            '#size' => 10,
            '#drup_context' => 'und'
        ];
        $form[$this->formContainer]['contact']['contact_infos']['contact_infos_city'] = [
            '#type' => 'textfield',
            '#title' => $this->t('City'),
            '#drup_context' => 'und'
        ];
        $form[$this->formContainer]['contact']['contact_infos']['contact_infos_country'] = [
            '#type' => 'select',
            '#title' => $this->t('Country'),
            '#options' => CountryManager::getStandardList(),
            '#drup_context' => 'und'
        ];
        $form[$this->formContainer]['contact']['contact_infos']['contact_infos_email'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Email'),
            '#drup_context' => 'und'
        ];
        $form[$this->formContainer]['contact']['contact_infos']['contact_infos_schedule'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Horaires'),
            '#drup_context' => 'und'
        ];

        // ...

        // Set #default_values for form items with '#drup_context' keys
        $this->populateDefaultValues($form, $form_state);

        return $form;
    }

    /**
     * @inheritDoc
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $values = $form_state->getValues();
        $drupSocialLinksConfig = DrupSocialLinks::getConfig(true);
        $drupSocialLinksItems = $drupSocialLinksConfig->get('items');

        foreach ($values as $key => $value) {
            // Custom social links
            if (strpos($key, 'site_social_link_') !== false) {
                $socialId = str_replace('site_social_link_', '', $key);

                if (isset($drupSocialLinksItems[$socialId])) {
                    $drupSocialLinksItems[$socialId]['link_url'] = $value;
                    $drupSocialLinksConfig->set('items', $drupSocialLinksItems);
                    $drupSocialLinksConfig->save();
                }
            } else {
                if (isset($this->formItemsData[$key])) {
                    // Default values
                    if ($this->formItemsData[$key]->context !== null) {
                        $this->drupSettings->setLanguage($this->formItemsData[$key]->context);
                    } else {
                        $this->drupSettings->setLanguage();
                    }
                    $this->drupSettings->set($key, $value);

                    // Files
                    if (in_array($this->formItemsData[$key]->type, ['managed_file'])) {
                        DrupFile::setPermanent($value);
                    }
                }
            }
        }

        $this->drupSettings->save();

        parent::submitForm($form, $form_state);
    }
}
