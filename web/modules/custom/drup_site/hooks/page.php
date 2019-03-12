<?php

use Drupal\drup\DrupCommon;
use Drupal\drup\DrupSEO;

/**
 * @inheritdoc
 */
function drup_site_page_attachments_alter(array &$attachments) {
    DrupCommon::removeHeaderLinks($attachments);
    DrupSEO::attachmentsAlter($attachments);
}
