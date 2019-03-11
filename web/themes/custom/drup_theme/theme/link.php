<?php


/**
 * @inheritdoc
 */
function drup_theme_preprocess_links(&$variables) {
    if ($variables['theme_hook_original'] === 'links__language_block') {
        $variables['attributes']['class'] = ['nav', 'nav--language'];

        if (!empty($variables['links'])) {
            foreach ($variables['links'] as $linkLangCode => $link) {
                if (isset($link['link'], $link['attributes'])) {
                    $link['attributes']->addClass('language-link');

                    $link['link']['#options']['attributes'] = $link['attributes'];
                    unset($variables['links'][$linkLangCode]['attributes']);
                }
            }
        }
    }
}
