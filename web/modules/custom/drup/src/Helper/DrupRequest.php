<?php

namespace Drupal\drup\Helper;

use Drupal\Core\Url;

/**
 * Class DrupRequest
 *
 * @package Drupal\drup\Helper
 */
abstract class DrupRequest {

    /**
     * @return mixed
     */
    public static function getCurrentTitle() {
        $request = \Drupal::request();
        $route = \Drupal::routeMatch()->getRouteObject();

        return \Drupal::service('title_resolver')->getTitle($request, $route);
    }

    /**
     * @return mixed
     */
    public static function isAdminRoute() {
        $adminContext = \Drupal::service('router.admin_context');

        return $adminContext->isAdminRoute();
    }

    /**
     * @return \Drupal\Component\Render\FormattableMarkup
     */
    public static function get404Content() {
        $drupRouter = \Drupal::service('drup_router.router');

        $content404 = '<h2 class="title--h3">' . t('You may have followed a broken link, or tried to view a page that no longer exists.') . '</h2>';
        if ($contact = $drupRouter->getPath('contact')) {
            $content404 .= '<p>' . t('If the problem persists, <a href="%link">contact us</a>.', ['%link' => $contact]) . '</p>';
        }
        $content404 .= '<p><a href="' . Url::fromRoute('<front>')
                ->toString() . '" class="btn btn--primary">' . t('Back to the front page') . '</a></p>';

        return new \Drupal\Component\Render\FormattableMarkup($content404, []);
    }

    /**
     * @return array
     */
    public static function getArgs() {
        return \Drupal::routeMatch()->getParameters()->all();
    }

    /**
     * @return string|null
     */
    public static function getRouteName() {
        return \Drupal::routeMatch()->getRouteName();
    }

    /**
     * @return mixed
     */
    public static function isFront() {
        return \Drupal::service('path.matcher')->isFrontPage();
    }
}
