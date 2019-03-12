<?php

use Drupal\Core\Url;
use Drupal\drup\DrupCommon;

/**
 * @inheritdoc
 */
function drup_site_language_switch_links_alter(array &$links, $type, $path) {
    $currentEntity = DrupCommon::getPageEntity(true);
    $isEntity = ($currentEntity->entity !== null);

    foreach ($links as $langCode => &$link) {
        $link['title'] = ucfirst($langCode);

        // Redirect untranslated entity to <front>
        if ($isEntity && method_exists($currentEntity->entity, 'getTranslation')) {
            try {
                if ($currentEntity->entity->getTranslation($langCode)->access('view')) {
                    //
                }
            }
            catch (\InvalidArgumentException $e) {
                $link['attributes']['class'][] = 'not-translated';
                $link['url'] = Url::fromRoute('<front>', [], ['language' => Drupal::languageManager()->getLanguage($langCode)]);
            }
        }
    }
}
