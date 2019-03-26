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
     * Tronquer
     *
     * @param $string
     * @param int $maxLength
     * @param string|bool $stripTags false pour désactiver
     *
     * @return string
     */
    public static function truncate($string, $maxLength = 250, $stripTags = null) {
        if ($stripTags !== false) {
            $string = strip_tags($string, $stripTags);
        }

        $string = str_replace(PHP_EOL, '', $string);

        if ($maxLength > 0) {
            $string = Unicode::truncate($string, $maxLength, true, true);
        }

        return $string;
    }
}