<?php

use Drupal\Core\Form\FormStateInterface;

/**
 * @inheritdoc
 */
function drup_form_alter(&$form, FormStateInterface $form_state, $form_id) {
    /**
     * User form : Fix role_delegation module
     * Remove role checkboxes if not permitted
     */
    if (isset($form['#form_id']) && $form['#form_id'] === 'user_form') {
        if (isset($form['account']['roles'])) {
            $user = \Drupal::currentUser();

            if (!$user->hasPermission('assign all roles')) {
                foreach ($form['account']['roles']['#options'] as $role => $roleName) {
                    if (!$user->hasPermission('assign ' . $role . ' role')) {
                        unset($form['account']['roles']['#options'][$role]);
                    }
                }
            }
        }
    }
}
