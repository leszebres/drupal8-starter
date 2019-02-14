<?php

/**
 * Implements hook_module_implements_alter().
 * @param $implementations
 * @param $hook
 */
function drup_module_implements_alter(&$implementations, $hook) {
    if ($hook === 'page_attachments_alter') {
        $group = $implementations['drup'];
        unset($implementations['drup']);
        $implementations['drup'] = $group;
    }
}