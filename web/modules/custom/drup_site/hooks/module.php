<?php

/**
 * @inheritdoc
 */
function drup_site_module_implements_alter(&$implementations, $hook) {
    if ($hook === 'page_attachments_alter') {
        $group = $implementations['drup'];
        unset($implementations['drup']);
        $implementations['drup'] = $group;
    }
}
