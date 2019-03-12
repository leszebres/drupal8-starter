<?php

namespace Drupal\drup\Helper;

use Drupal\drup_settings\DrupSettings;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DrupUrl
 *
 * Méthodes globales pour le traitement des urls
 *
 * @package Drupal\drup\Helper
 */
abstract class DrupUrl {

    /**
     * Remplacement d'un argument dans la queryString
     *
     * @param $argument
     * @param $value
     * @param $queryString
     * @return string
     */
    public static function replaceArgument($argument, $value, $queryString): string {
        $separator = !empty($queryString) ? '&' : null;
        $replace = (strpos($queryString, $argument) !== false);

        return '?' . ($replace ? preg_replace('/' . $argument . '\=[a-z0-9]+/i', $argument . '=' . $value, $queryString) : $queryString . $separator . $argument . '=' . $value);
    }

    /**
     * @param null $relativePath
     * @param null $baseUrl
     *
     * @return string
     */
    public static function getAbsolutePath($relativePath = null, $baseUrl = null)
    {
        if (empty($baseUrl)) {
            $baseUrl = Request::createFromGlobals()->getSchemeAndHttpHost();
        }
        if (empty($relativePath)) {
            $relativePath = \Drupal::service('path.current')->getPath();
        }

        return $baseUrl . $relativePath;
    }

    /**
     * @param bool $forceLoad
     *
     * @return array
     */
    public static function getSocialLinks($forceLoad = true)
    {
        $socialNetworks = ['facebook', 'twitter', 'linkedin', 'youtube'];
        $drupSettings = new DrupSettings();

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
     * @return array
     */
    public static function getShareItems()
    {
        $config = \Drupal::config('system.site');
        $request = \Drupal::request();
        $route_match = \Drupal::routeMatch();

        $title = $config->get('name') . ' : ' . \Drupal::service('title_resolver')
                ->getTitle($request, $route_match->getRouteObject());
        $currentTitle = urlencode($title);
        $pathAlias = \Drupal::service('path.alias_manager')
            ->getAliasByPath($request->getPathInfo());
        $currentUrl = urlencode($request->getSchemeAndHttpHost() . $request->getBaseUrl() . $pathAlias);

        return [
            'linkedin' => [
                'url' => 'https://www.linkedin.com/shareArticle?url=' . $currentUrl . '&title=' . $currentTitle,
                'icon' => 'linkedin',
            ],
            'twitter' => [
                'url' => 'https://twitter.com/share?url=' . $currentUrl . '&text=' . $currentTitle,
                'icon' => 'twitter',
            ],
            'facebook' => [
                'url' => 'https://www.facebook.com/sharer/sharer.php?u=' . $currentUrl . '&t=' . $currentTitle,
                'icon' => 'facebook',
            ],
        ];
    }
}
