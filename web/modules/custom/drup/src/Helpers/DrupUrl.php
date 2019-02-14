<?php

namespace Drupal\drup\Helpers;

/**
 * Class DrupUrl
 *
 * Méthodes globales pour le traitement des urls
 *
 * @package Drupal\drup\Helpers
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
}