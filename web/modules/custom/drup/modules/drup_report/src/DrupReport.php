<?php

namespace Drupal\drup_report;

use Drupal\Component\Utility\Html;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Config\Config;
use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Database\Database;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\dblog\Controller\DbLogController;
use Drupal\user\Entity\User;

/**
 * Class DrupReport
 *
 * @package Drupal\drup_report
 */
class DrupReport {

    /**
     * Nom de la configuration
     *
     * @var string
     */
    protected static $configName = 'drup.drup_report';

    /**
     * Retourne le nom de la configuration
     *
     * @return string
     */
    public static function getConfigName() {
        return self::$configName;
    }

    /**
     * Retourne la configuration des items (lecture ou écriture)
     *
     * @param bool $editable
     *
     * @return bool|Config|ImmutableConfig
     */
    public static function getConfig($editable = true) {
        $config = $editable ? \Drupal::service('config.factory')->getEditable(self::getConfigName()) : \Drupal::config(self::getConfigName());

        if (($editable && $config instanceof Config )|| (!$editable && $config instanceof ImmutableConfig)) {
            return $config;
        }

        return false;
    }

    /**
     * @param null $userId
     *
     * @return array
     */
    public static function getConfigValuesByUser($userId = null) {
        if ($userId === null) {
            $userId = \Drupal::currentUser()->id();
        }

        $config = self::getConfig();
        return $config->get('data')[$userId] ?? [];
    }

    /**
     *
     */
    public static function sendReports() {
        $config = self::getConfig();
        $configValues = $config->get('data');

        if (!empty($configValues)) {
            foreach ($configValues as $userId => &$userConfig) {
                $shouldSend = false;
                $currentTimestamp = (new DrupalDateTime)->format('U');

                if (!isset($userConfig['date_last_send'])) {
                    $shouldSend = true;
                    $userConfig['date_last_send'] = $currentTimestamp;

                } else {
                    $lastSendDateTime = (new DrupalDateTime)->setTimestamp($userConfig['date_last_send']);
                    if ($lastSendDateTime->modify('+1 ' . $userConfig['periodicity'])->format('U') <= $currentTimestamp) {
                        $shouldSend = true;
                    }
                }

                if ($shouldSend && $sent = self::sendReport($userId, $userConfig)) {
                    $userConfig['date_last_send'] = $currentTimestamp;
                }
            }
            unset($userConfig);

            $config->set('data', $configValues)->save();
        }
    }

    /**
     * @param $userId
     * @param array $userConfig
     *
     * @return bool
     */
    public static function sendReport($userId, array $userConfig) {
        if (($user = User::load($userId)) && $logs = self::getLogs($userConfig['types'], $userConfig['date_last_send'])) {
            $langcode = $user->getPreferredLangcode();
            $to = $userConfig['email'] ?? $user->getEmail();

            $params = [
                'headers' => []
            ];
            $renderParams = [
                '#theme' => 'drup_report__email',
                '#data' => $logs
            ];
            $params['message'] = \Drupal::service('renderer')->renderPlain($renderParams);

            if ($result = \Drupal::service('plugin.manager.mail')->mail('drup_report', 'drup_report_email', $to, $langcode, $params, null, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param $types
     * @param $timestamp
     *
     * @return array
     */
    public static function getLogs($types, $timestamp) {
        $logs = [];
        $database = Database::getConnection();
        $query = $database->select('watchdog', 'w');
        $query->fields('w');

        if (!empty($types)) {
            $query->condition('w.type', $types, 'IN');
        }

        $query->condition('w.timestamp', $timestamp, '>=');
        $query->orderBy('w.wid', 'DESC');
        $query->range(0, 200);

        if (($result = $query->execute()) && $logs = $result->fetchAll()) {
            $controller = DbLogController::create(\Drupal::getContainer());
            $dateFormatter = \Drupal::service('date.formatter');
            $classes = $controller::getLogLevelClassMap();

            foreach ($logs as $log) {
                $log->content = $controller->formatMessage($log);
                $log->date = $dateFormatter->format($log->timestamp, 'custom', 'd/m/Y H:i');
                $log->title = Unicode::truncate(Html::decodeEntities(strip_tags($log->content)), 200, TRUE, TRUE);
                $log->url = \Drupal\Core\Url::fromRoute('dblog.event', [
                    'event_id' => $log->wid,
                ]);
                $log->severity_label = $classes[$log->severity];

                $logs[] = $log;
            }
        }

        return $logs;
    }
}
