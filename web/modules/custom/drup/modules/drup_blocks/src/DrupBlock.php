<?php

namespace Drupal\drup_blocks;

/**
 * Class DrupBlocks
 * @package Drupal\drup_blocks
 */
abstract class DrupBlock {
    
    /**
     * Format block theme
     * @param $themes
     * @param array $options
     */
    public static function format(&$themes, $options = []) {
        $options = array_merge([
            'type' => 'blocks'
        ], $options);
    
        $themePath = '/' . drupal_get_path('theme', 'drup_theme');
        $themePathBlocks = $themePath . '/templates/' . $options['type'];
    
        foreach ($themes as $themeID => &$theme) {
            if (strpos($themeID, 'drup_' . $options['type']) !== false) {
            
                // Admin
                if (strpos($themeID, 'drup_' . $options['type'] . '_admin') !== false) {
                    $template = str_replace('drup_' . $options['type'] . '_admin_', '', $themeID);
                    $theme['variables']['admin_url'] = null;
                } else {
                    $template = str_replace('drup_' . $options['type'] . '_', '', $themeID);
                }
            
                $theme['path'] = $themePathBlocks;
                $theme['template'] = str_replace('_', '-', strtolower($template));
                $theme['variables']['theme_path'] = $themePath;
            }
        }
    }
}
