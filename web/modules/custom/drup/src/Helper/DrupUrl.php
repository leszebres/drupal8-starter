<?php

namespace Drupal\drup\Helper;

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
}
