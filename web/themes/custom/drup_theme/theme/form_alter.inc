<?php

use Drupal\drup_site\DrupSiteForm;

/**
 * @param $form
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 * @param $form_id
 */
function drup_theme_form_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, $form_id) {
    // WEBFORM (CONTACT)
    if (strpos($form_id, 'webform_submission_') !== false) {
        $form['#attributes']['class'][] = 'form'; // Ne pas remplacer les classes par défaut, elles sont utilisées par webform (conditions)
        $form['#attributes']['class'][] = 'form--webform';

        // RGPD
        DrupSiteForm::setGDPRMention($form);
    }

    // LOGIN + PASSWORD
    elseif (in_array($form_id, ['user_login_form', 'user_pass'])) {
        $form['#attributes']['class'][] = 'form';
        $form['#attributes']['class'][] = 'form--login';
    }
}

/**
 * @param $form
 * @param \Drupal\Core\Form\FormStateInterface $form_state
 * @param $form_id
 */
function drup_theme_form_views_exposed_form_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, $form_id) {
    // Vue Tags : articles dans une thématique/notion clé
    if ($form['#id'] === 'views-exposed-form-healthreview-tags-articles-term') {

    }

    // Force le bouton submit en balise <input>
    $form['actions']['submit']['#button_type'] = 'input';
}