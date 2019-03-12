<?php

use Drupal\Core\Url;
use Drupal\drup\DrupCommon;
use Drupal\drup\DrupPageEntity;

/**
 * @inheritdoc
 */
function drup_site_language_switch_links_alter(array &$links, $type, $path) {
    $currentEntity = DrupPageEntity::getPageEntity(true);
    $isEntity = ($currentEntity->entity !== null);

    foreach ($links as $languageId => &$link) {
        $link['title'] = ucfirst($languageId);

        // Redirect untranslated entity to <front>
        if ($isEntity && method_exists($currentEntity->entity, 'getTranslation')) {
            try {
                if ($currentEntity->entity->getTranslation($languageId)->access('view')) {
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
