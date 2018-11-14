<?php

namespace Drupal\drup;

use Drupal\file\Entity\File;
use Drupal\node\Entity\Node;
use Drupal\Core\Template\Attribute;
use Drupal\taxonomy\Entity\Term;
use Drupal\Core\Url;

/**
 * Class DrupSite
 * @package Drupal\drup
 */
class DrupSite {
    
    /**
     * @return \Drupal\Component\Render\FormattableMarkup
     */
    public static function get404Content() {
        $drupRouter = \Drupal::service('drup_router.router');
        
        $content404 = '<h2 class="title--h3">'.t('You may have followed a broken link, or tried to view a page that no longer exists.').'</h2>';
        if ($contact = $drupRouter->getPath('contact')) {
            $content404 .= '<p>'.t('If the problem persists, <a href="@link">contact us</a>.', ['@link' => $contact]).'</p>';
        }
        $content404 .= '<p><a href="'.Url::fromRoute('<front>')->toString().'" class="btn btn--primary">'.t('Back to the front page').'</a></p>';
        
        return new \Drupal\Component\Render\FormattableMarkup($content404, []);
    }
    
    /**
     * @return array
     */
    public static function getSocialLinks($forceLoad = true) {
        $socialNetworks = ['facebook', 'twitter', 'linkedin', 'youtube'];
        $drupSettings = \Drupal::service('drup_settings.variables');
        
        $links = [];
        foreach ($socialNetworks as $socialNetwork) {
            $url = $drupSettings->getValue('site_' . $socialNetwork);
            
            if ($forceLoad === false && empty($url)) {
                continue;
            }
            
            $links[$socialNetwork] = [
                'url' => $url,
                'title' => ucfirst($socialNetwork)
            ];
        }
        
        return $links;
    }
    
    /**
     * Liste des réseaux sociaux pour partager un article
     *
     * @return array
     */
    public static function getShareItems() {
        $config = \Drupal::config('system.site');
        $request     = \Drupal::request();
        $route_match = \Drupal::routeMatch();
        
        $title       = $config->get('name') . ' : '. \Drupal::service('title_resolver')->getTitle($request, $route_match->getRouteObject());
        $currentTitle = urlencode($title);
        $pathAlias = \Drupal::service('path.alias_manager')->getAliasByPath($request->getPathInfo());
        $currentUrl = urlencode($request->getSchemeAndHttpHost() . $request->getBaseUrl() . $pathAlias);
        
        return [
            'linkedin' => [
                'url'  => 'https://www.linkedin.com/shareArticle?url=' . $currentUrl . '&title=' . $currentTitle,
                'icon' => 'linkedin'
            ],
            'twitter'  => [
                'url'  => 'https://twitter.com/share?url=' . $currentUrl . '&text=' . $currentTitle,
                'icon' => 'twitter'
            ],
            'facebook' => [
                'url'  => 'https://www.facebook.com/sharer/sharer.php?u=' . $currentUrl . '&t=' . $currentTitle,
                'icon' => 'facebook'
            ]
        ];
    }
}
