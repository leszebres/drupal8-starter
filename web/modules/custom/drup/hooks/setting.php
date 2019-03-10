<?php

/**
 * @inheritdoc
 */
function drup_editor_js_settings_alter(array &$settings) {
    $settings['editor']['formats']['html_basic']['editorSettings']['bodyClass'] = ['node-body'];
    $settings['editor']['formats']['html_basic']['editorSettings']['format_tags'] = 'p;h2;h3;h4';
}
