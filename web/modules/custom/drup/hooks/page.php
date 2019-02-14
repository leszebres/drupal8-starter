<?php

use Drupal\drup\DrupCommon;
use Drupal\drup\DrupSEO;

/**
 * Implements hook_page_attachments_alter().
 * @param array $attachments
 */
function drup_page_attachments_alter(array &$attachments) {
    DrupCommon::removeHeaderLinks($attachments);
    DrupSEO::attachmentsAlter($attachments);
}