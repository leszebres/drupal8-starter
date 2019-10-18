<?php

use Drupal\drup\DrupSEO;

/**
 * @param array $links
 * @param       $sitemap_variant
 */
function drup_site_simple_sitemap_links_alter(array &$links, $sitemap_variant) {
    DrupSEO::sitemapxmlAddImages($links);
}