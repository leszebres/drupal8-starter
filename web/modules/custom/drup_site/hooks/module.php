<?php

/**
 * {@inheritdoc}
 */
function drup_site_module_implements_alter(&$implementations, $hook) {
    if ($hook === 'page_attachments_alter') {
        $group = $implementations['drup_site'];
        unset($implementations['drup_site']);
        $implementations['drup_site'] = $group;
    }
}