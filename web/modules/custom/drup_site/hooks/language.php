<?php

use Drupal\Core\Url;
use Drupal\drup\DrupPageEntity;

/**
 * @inheritdoc
 */
function drup_site_language_switch_links_alter(array &$links, $type, $path) {
    $drupPageEntity = DrupPageEntity::loadEntity();
    $entity = $drupPageEntity->getEntity();

    foreach ($links as $languageId => &$link) {
        $link['title'] = ucfirst($languageId);

        // Redirect untranslated entity to <front>
        if (method_exists($entity, 'getTranslation')) {
            try {
                if ($entity->getTranslation($languageId)->access('view')) {
                    //
                }
            }
            catch (\InvalidArgumentException $e) {
                $link['attributes']['class'][] = 'not-translated';
                $link['url'] = Url::fromRoute('<front>', [], ['language' => Drupal::languageManager()->getLanguage($languageId)]);
            }
        }
    }
}
