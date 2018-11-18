<?php

namespace Drupal\drup_blocks;

use Drupal\Core\Url;

use Drupal\drup\DrupCommon;

/**
 * Class DrupBlockAdmin
 *
 * @package Drupal\drup_blocks
 */
abstract class DrupBlockAdmin {

    public static $contextKey = 'drup-blocks-context';

    /**
     * @param string $view
     *
     * @return mixed|null|string
     */
    public static function getContext($view = 'front') {
        if ($view === 'admin') {
            return \Drupal::request()->query->get(self::$contextKey);
        }

        if (\Drupal::service('path.matcher')->isFrontPage()) {
            return 'front';
        }

        if ($entity = DrupCommon::getPageEntity()) {
            return $entity->type . '/' . $entity->id;
        }

        return null;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public static function getValues($key) {
        return \Drupal::service('config.factory')
            ->getEditable('drup_blocks.admin_values')
            ->get($key);
    }

    /**
     * @param $key
     * @param $values
     */
    public static function setValues($key, $values) {
        $service = \Drupal::service('config.factory')
            ->getEditable('drup_blocks.admin_values');
        $service->set($key, $values);
        $service->save();
    }

    /**
     * @param $blockId
     * @param string $view
     *
     * @return mixed|null
     * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
     * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
     */
    public static function getContextedValues($blockId, $view = 'admin') {
        if ($context = self::getContext($view)) {
            $key = self::formatContextedKey($blockId, $context);

            return self::getValues($key);
        }

        return null;
    }

    /**
     * @param $blockID
     *
     * @return \Drupal\Core\Url|null
     * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
     * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
     */
    public static function getAdminConfigUrl($blockID) {
        if (\Drupal::currentUser()->hasPermission('administer blocks')) {
            $context = self::getContext('front');

            return Url::fromRoute('entity.block.edit_form', ['block' => $blockID], [
                'query' => [
                    'destination' => \Drupal::destination()->get(),
                    self::$contextKey => $context,
                ],
            ]);
        }

        return null;
    }

    /**
     * @param $blockID
     * @param $context
     *
     * @return string
     */
    public static function formatContextedKey($blockID, $context) {
        $currentLanguage = \Drupal::languageManager()
            ->getCurrentLanguage()
            ->getId();

        return $blockID . '.' . $currentLanguage . '.' . $context;
    }
}
