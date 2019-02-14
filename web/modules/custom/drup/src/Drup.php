<?php

namespace Drupal\drup;

/**
 * Class Drup
 *
 * @package Drupal\drup
 */
class Drup {

    /**
     * @return string
     */
    public static function getModuleName() {
        return 'drup';
    }

    /**
     * @return string
     */
    public static function getModulePath() {
        return drupal_get_path('module', self::getModuleName());
    }

    /**
     * Implements hooks
     */
    public static function implementsHooks() {
        $dirname = 'hooks';
        $path = self::getModulePath() . '/' . $dirname;
        $dir = scandir($path, SCANDIR_SORT_NONE);
        $moduleHandler = \Drupal::moduleHandler();

        if (!empty($dir)) {
            foreach ($dir as $item) {
                if ($item !== '.' && $item !== '..') {
                    $moduleHandler->loadInclude(self::getModuleName(), 'php', $dirname . '/' . substr($item, 0, -4));
                }
            }
        }
    }
}