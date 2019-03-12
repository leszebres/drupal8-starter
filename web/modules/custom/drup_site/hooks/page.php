<?php

use Drupal\drup\DrupCommon;
use Drupal\drup\DrupHead;
use Drupal\drup\DrupSEO;

/**
 * @inheritdoc
 */
function drup_site_page_attachments_alter(array &$attachments) {
    DrupHead::removeHeaderLinks($attachments);
    DrupSEO::attachmentsAlter($attachments);
}
