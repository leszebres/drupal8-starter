<?php

use Drupal\Core\Url;
use Drupal\Core\Link;

/**
 * Media "file" template
 * @param $variables
 */
function drup_theme_preprocess_file_link(&$variables) {
    $linkHtml = $variables['link']->getGeneratedLink();
    preg_match('/<a.*?>(.+?)<\/a>/i', $linkHtml, $linkText);
    preg_match('@href="(.+)"@Um', $linkHtml, $linkHref);
    
    if (isset($linkText[1]) && !empty($linkHref[1])) {
        $url = Url::fromUri($linkHref[1]);
        $url->setOptions(['attributes' => [
            'class' => ['btn', 'btn--primary'],
            'target' => '_blank'
        ]]);
        $link = Link::fromTextAndUrl($linkText[1], $url)->toString();
        $variables['link'] = $link;
    }
}
