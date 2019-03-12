<?php

namespace Drupal\drup\Helper;

use Drupal\Component\Utility\Unicode;

/**
 * Class DrupString
 *
 * Méthodes globales pour le traitement des chaines de caractères
 *
 * @package Drupal\drup\Helper
 */
abstract class DrupString {

    /**
     * @param $string
     * @param int $truncateLimit
     * @param bool $tagsToStrip
     *
     * @return string
     */
    public static function trimString($string, $truncateLimit = 250, $tagsToStrip = true)
    {
        if ($tagsToStrip !== false) {
            $string = strip_tags($string, $tagsToStrip);
        }

        $string = str_replace(PHP_EOL, '', $string);

        if ($truncateLimit > 0) {
            $string = Unicode::truncate($string, $truncateLimit, true, true);
        }

        return $string;
    }
}
