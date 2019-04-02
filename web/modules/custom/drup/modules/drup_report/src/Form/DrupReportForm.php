<?php

namespace Drupal\drup_report\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\drup_report\DrupReport;

/**
 * Class DrupReportForm.
 */
class DrupReportForm extends ConfigFormBase {

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
        return [DrupReport::getConfigName()];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'drup_report_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $user = \Drupal::currentUser();
        $config = DrupReport::getConfigValuesByUser($user->id());

        $form['user_id'] = [
            '#type' => 'hidden',
            '#value' => $user->id(),
        ];
        $form['user_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('User'),
            '#default_value' => $user->getDisplayName(),
            '#attributes' => [
                'readonly' => 'readonly',
                'disabled' => 'disabled',
            ],
        ];

        $form['enable'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Enable feature'),
            '#default_value' => !empty($config),
        ];

        $types = [];
        foreach (_dblog_get_message_types() as $type) {
            $types[$type] = t($type);
        }

        $form['config'] = [
            '#type' => 'fieldset',
            '#title' => $this->t('Configuration'),
            '#states' => [
                'visible' => [
                    ':input[name="enable"]' => ['checked' => true],
                ],
            ],
        ];
        $form['config']['types'] = [
            '#type' => 'checkboxes',
            '#title' => $this->t('Types'),
            '#description' => $this->t('If none selected, all will be available.'),
            '#options' => $types,
            '#default_value' => $config['types'] ?? []
        ];

        $form['config']['periodicity'] = [
            '#type' => 'select',
            '#title' => $this->t('Periodicity'),
            '#options' => [
                'hour' => $this->t('Hourly'),
                'day' => $this->t('Daily'),
                'week' => $this->t('Weekly'),
                'month' => $this->t('Monthly'),
            ],
            '#default_value' => $config['periodicity'] ?? null,
        ];

        $form['config']['email'] = [
            '#type' => 'email',
            '#title' => $this->t('Email override'),
            '#description' => $this->t('Instead, "@key" will be used', ['@key' => $user->getEmail()]),
            '#default_value' => $config['email'] ?? null,
        ];

        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $config = $this->config(DrupReport::getConfigName());
        $userId = $form_state->getValue('user_id');

        $userData = [];
        if ((bool) $form_state->getValue('enable') === true) {
            $userData = [
                'types' => array_filter($form_state->getValue('types')),
                'periodicity' => $form_state->getValue('periodicity'),
                'email' => $form_state->getValue('email'),
                'user' => $userId,
            ];
        }

        $data = $config->get('data');
        $data[$userId] = $userData;

        $config->set('data', $data)->save();

        parent::submitForm($form, $form_state);
    }
}
