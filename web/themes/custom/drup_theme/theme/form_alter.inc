<?php

use Drupal\Core\Link;
use Drupal\drup\Views\DrupViews;
use Drupal\drup_site\DrupSiteForm;

/**
 * @inheritdoc
 */
function drup_theme_form_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, $form_id) {
    // WEBFORM (CONTACT)
    if (strpos($form_id, 'webform_submission_') !== false) {
        $form['#attributes']['class'][] = 'form'; // Ne pas remplacer les classes par défaut, elles sont utilisées par webform (conditions)
        $form['#attributes']['class'][] = 'form--webform';
        $form['#attributes']['class'][] = 'form--' . str_replace('_', '-', $form['#webform_id']);

        // RGPD
        DrupSiteForm::setGDPRMention($form);
    }

    // LOGIN + PASSWORD
    elseif (\in_array($form_id, ['user_login_form', 'user_pass'])) {
        $form['#attributes']['class'][] = 'form';
        $form['#attributes']['class'][] = 'form--login';

        // Reset password
        $form['actions']['reset'] = [
            '#type' => 'markup',
            '#markup' => Link::createFromRoute(t($form_id === 'user_pass' ? 'Cancel' : 'Reset password'), 'user.' . ($form_id === 'user_pass' ? 'login' : 'pass'))->toString(),
            '#prefix' => '<div class="js-form-item form-item js-form-type-markup form-item-reset js-form-item-reset">',
            '#suffix' => '</div>'
        ];

        // Remove useless description
        unset(
            $form['name']['#description'],
            $form['pass']['#description']
        );
    }
}

/**
 * @inheritdoc
 */
function drup_theme_form_views_exposed_form_alter(&$form, \Drupal\Core\Form\FormStateInterface $form_state, $form_id) {
    /** @var \Drupal\views\ViewExecutable $viewExecutable */
    $viewExecutable = $form_state->get('view');
    /** @var \Drupal\views\Entity\View $view */
    $view = $form_state->get('view')->storage;

    // View type + bundle
    [
        'type' => $viewEntityType,
        'bundle' => $viewEntityBundle,
        'base_field' => $viewBaseField
    ] = DrupViews::getBaseEntityInfo($viewExecutable);


    // Vue Tags : articles dans une thématique/notion clé
    if ($form['#id'] === 'views-exposed-form-healthreview-tags-articles-term') {

    }

    // Libraries
    //$form['#attached']['library'][] = 'drup_theme/form-default-filters';
    //$form['#attached']['drupalSettings']['form_filters_id'] = $form['#id'];

    // Force le bouton submit en balise <input>
    $form['actions']['submit']['#button_type'] = 'input';
}
