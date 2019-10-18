<?php

use Drupal\drup\DrupSEO;

/**
 * {@inheritdoc}
 */
function drup_site_token_info() {
    $info = [];

    // SEO
    DrupSEO::tokensInfo($info);

    return $info;
}

/**
 * @inheritdoc
 */
function drup_site_tokens($type, $tokens, array $data, array $options, \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata) {
    $replacements = [];

    // SEO
    DrupSEO::tokens($replacements, $type, $tokens, $data, $options, $bubbleable_metadata);

    return $replacements;
}

/**
 * @inheritdoc
 */
function drup_site_tokens_alter(array &$replacements, array $context) {
    // SEO
    DrupSEO::tokensAlter($replacements, $context);
}
