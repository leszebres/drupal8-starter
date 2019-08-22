<?php

namespace Drupal\drup_site\EventSubscriber;

use Drupal\Core\Entity\EntityBase;
use Drupal\Core\Url;
use Drupal\drup\Entity\Node;
use Drupal\drup\Helper\DrupString;
use Drupal\media\Entity\Media;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class Redirections
 *
 * @package Drupal\drup_site\EventSubscriber
 */
class Redirections implements EventSubscriberInterface {

    /**
     * @var \Drupal\Core\Session\AccountProxyInterface
     */
    private $currentUser;

    /**
     * @var string
     */
    private $languageId;

    /**
     * @var \Drupal\Core\Entity\EntityBase
     */
    private $entity;

    /**
     * @var \Drupal\Core\Url
     */
    private $frontUrl;

    /**
     * Redirections selon les types de Node
     *
     * @param \Drupal\drup\Entity\Node $entity
     *
     * @return Url[]
     */
    public static function getNodeRedirections(Node $entity) {
        $data = [
            //'factory' => $this->frontUrl,
        ];

        return $data;
    }

    /**
     * Redirections selon les types de Media
     *
     * @param \Drupal\media\Entity\Media $entity
     *
     * @return array
     */
    public static function getMediaRedirections(Media $entity) {
        $data = [
            // Documents vers l'url de téléchargement direct
            'document' => Url::fromRoute('drup_site.media_entity_download', ['media' => $entity->id()])
        ];

        return $data;
    }

    /**
     * @param string $type
     * @param string $bundle
     *
     * @return \Drupal\Core\Url|null
     */
    public static function getEntityRedirectionUrl(string $type, EntityBase $entity) {
        $methodName = 'get' . DrupString::toCamelCase($type) . 'Redirections';

        if (method_exists(__CLASS__, $methodName)) {
            $redirections = self::{$methodName}($entity);
            return $redirections[$entity->bundle()] ?? null;
        }


        return null;
    }

    /**
     * Constructs a new RedirectAnonymousSubscriber object.
     */
    public function __construct() {
        $this->languageId = \Drupal::languageManager()->getCurrentLanguage()->getId();
        $this->currentUser = \Drupal::currentUser();

        $this->frontUrl = Url::fromRoute('<front>');
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents() {
        return [KernelEvents::REQUEST => [['execute']]];
    }

    /**
     * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
     */
    public function execute(GetResponseEvent $event) {
        $request = $event->getRequest();

        // Nodes
        if ($request->attributes->get('_route') === 'entity.node.canonical') {
            $this->entity = $request->attributes->get('node');

            // Custom ...

            // Default
            if ($url = self::getEntityRedirectionUrl('node', $this->entity)) {
                $redirectionPath = $url instanceof Url ? $url->toString() : (string) $url;
            }
        }
        // Medias
        else if ($request->attributes->get('_route') === 'entity.media.canonical') {
            $this->entity = $request->attributes->get('media');

            // Custom ...

            // Default
            if ($url = self::getEntityRedirectionUrl('media', $this->entity)) {
                $redirectionPath = $url instanceof Url ? $url->toString() : (string) $url;
            }
        }

        // Set redirection
        if (isset($redirectionPath)) {
            $response = new RedirectResponse($redirectionPath, 301);
            $event->setResponse($response);
        }
    }

}
