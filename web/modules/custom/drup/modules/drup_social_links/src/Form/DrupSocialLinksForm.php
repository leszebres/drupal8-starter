<?php

namespace Drupal\drup_social_links\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\drup_social_links\DrupSocialLinks;

/**
 * Class RouterForm.
 */
class DrupSocialLinksForm extends ConfigFormBase {

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
        return [DrupSocialLinks::getConfigName()];
    }

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'drup_social_links_form';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $config = $this->config(DrupSocialLinks::getConfigName());
        $links = $config->get('items');

        $form['drup_social_links'] = [
            '#type' => 'table',
            '#header' => [
                $this->t('Link'),
                $this->t('Share'),
                $this->t('Title'),
                $this->t('Id'),
                $this->t('Link URL'),
                $this->t('Share URL'),
                $this->t('Options')
            ],
            '#empty' => $this->t('No links found')
        ];

        if (!empty($links)) {
            foreach ($links as $link) {
                $form['drup_social_links'][] = $this->setRow($link);
            }
        }
        $form['drup_social_links'][] = $this->setRow([
            'isNew' => true
        ]);

        $form['#attached']['library'][] = 'drup_social_links/form-links';

        $form['token_browser'] = [
            '#theme' => 'token_tree_link',
            '#token_types' => [
                'seo'
            ],
            '#global_types' => true,
            '#show_nested' => false
        ];

        return parent::buildForm($form, $form_state);
    }

    /**
     * @param $rowValues
     *
     * @return array
     */
    public function setRow($rowValues) {
        $row = [];
        $isNewRow = (isset($rowValues['isNew']) && $rowValues['isNew']);

        $row['link'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Link'),
            '#title_display' => !$isNewRow ? 'invisible' : 'before',
            '#default_value' => !$isNewRow ? $rowValues['link'] : false
        ];
        $row['share'] = [
            '#type' => 'checkbox',
            '#title' => $this->t('Share'),
            '#title_display' => !$isNewRow ? 'invisible' : 'before',
            '#default_value' => !$isNewRow ? $rowValues['share'] : false
        ];
        $row['title'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Title'),
            '#title_display' => !$isNewRow ? 'invisible' : 'before',
            '#default_value' => !$isNewRow ? $rowValues['title'] : null,
            '#attributes' => [
                'class' => ['form-item-title']
            ]
        ];
        $row['id'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Id'),
            '#title_display' => !$isNewRow ? 'invisible' : 'before',
            '#default_value' => !$isNewRow ? $rowValues['id'] : null,
            '#attributes' => [
                'class' => ['form-item-id']
            ]
        ];
        $row['link_url'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Link URL'),
            '#title_display' => !$isNewRow ? 'invisible' : 'before',
            '#default_value' => !$isNewRow && !empty($rowValues['link_url']) ? $rowValues['link_url'] : null
        ];
        $row['share_url'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Share URL'),
            '#title_display' => !$isNewRow ? 'invisible' : 'before',
            '#default_value' => !$isNewRow && !empty($rowValues['share_url']) ? $rowValues['share_url'] : null
        ];
        $row['options'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Options'),
            '#title_display' => !$isNewRow ? 'invisible' : 'before',
            '#default_value' => !$isNewRow && !empty($rowValues['options']) ? $rowValues['options'] : null,
            '#placeholder' => $isNewRow ? 'key=value, key2=value2, etc' : null,
            '#attributes' => [
                'class' => ['form-item-options']
            ]
        ];

        foreach ($row as $rowItemId => $rowItem) {
            $row[$rowItemId]['#size'] = '0';

            if (empty($row[$rowItemId]['#placeholder'])) {
                $row[$rowItemId]['#placeholder'] = $rowItem['#title'];
            }
        }

        return $row;
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $config = $this->config(DrupSocialLinks::getConfigName());
        $links = $form_state->getValue('drup_social_links');
        $saveLinks = [];

        if (!empty($links)) {
            foreach ($links as $link) {
                if (!empty($link['title']) && !empty($link['id'])) {
                    $saveLinks[$link['id']] = $link;
                }
            }
        }

        $config->set('items', $saveLinks);
        $config->save();

        parent::submitForm($form, $form_state);
    }
}