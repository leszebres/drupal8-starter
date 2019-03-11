<?php

use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * @inheritdoc
 */
function drup_theme_preprocess_file_link(&$variables) {
    if (method_exists($variables['link'], 'getGeneratedLink')) {
        $linkHtml = $variables['link']->getGeneratedLink();
        preg_match('/<a.*?>(.+?)<\/a>/i', $linkHtml, $linkText);
        preg_match('@href="(.+)"@Um', $linkHtml, $linkHref);

        if (isset($linkText[1]) && !empty($linkHref[1])) {
            $url = Url::fromUri($linkHref[1]);
            $htmlClasses = [];

            // File uploaded through webforms
            if (strpos($linkHref[1], 'webform') === false) {
                $htmlClasses[] = 'btn';
                $htmlClasses[] = 'btn--primary';
            }
            $url->setOptions(['attributes' => [
                'class' => $htmlClasses,
                'target' => '_blank'
            ]]);

            $variables['link'] = Link::fromTextAndUrl($linkText[1], $url)->toString();
        }
    }
}

/**
 * @inheritdoc
 */
function drup_theme_preprocess_file_managed_file(&$variables) {
    unset($variables['attributes']['id']);
}
