<?php

namespace Drupal\drup\Helper;

/**
 * Class DrupRequest
 *
 * @package Drupal\drup\Helper
 */
abstract class DrupRequest {

    /**
     * @return mixed
     */
    public static function getCurrentTitle()
    {
        $request = \Drupal::request();
        $route = \Drupal::routeMatch()->getRouteObject();

        return \Drupal::service('title_resolver')->getTitle($request, $route);
    }

    /**
     * @return mixed
     */
    public static function isAdminRoute()
    {
        $adminContext = \Drupal::service('router.admin_context');

        return $adminContext->isAdminRoute();
    }
}
