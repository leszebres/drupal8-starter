<?php

namespace Drupal\drup;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\drup\Entity\Term;
use Drupal\drup\Helper\DrupRequest;
use Drupal\drup\Entity\DrupField;

/**
 * Class DrupBreadcrumb
 *
 * @package Drupal\drup
 */
class DrupBreadcrumb implements BreadcrumbBuilderInterface {

    /**
     * @var \Drupal\drup\DrupPageEntity
     */
    protected $drupPageEntity;

    /**
     * @return array
     */
    public function buildList() {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function applies(RouteMatchInterface $route_match) {
        if (!DrupRequest::isAdminRoute()) {
            $this->drupPageEntity = DrupPageEntity::loadEntity();
            $breadcrumbItems = $this->buildList();

            if (!empty($this->drupPageEntity->getBundle()) && isset($breadcrumbItems[$this->drupPageEntity->getEntityType()]) && array_key_exists($this->drupPageEntity->getBundle(), $breadcrumbItems[$this->drupPageEntity->getEntityType()])) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     *
     * @link http://kevinquillen.com/drupal/2017/02/16/manually-add-breadcrumb-links-in-drupal-8
     */
    public function build(RouteMatchInterface $route_match) {
        /** @var \Drupal\drup_router\DrupRouterService $drupRouter **/
        $drupRouter = \Drupal::service('drup_router.router');
        $drupField = new DrupField($this->drupPageEntity->getEntity());

        $breadcrumbItemsList = $this->buildList();
        $breadcrumbItems = $breadcrumbItemsList[$this->drupPageEntity->getEntityType()][$this->drupPageEntity->getBundle()];

        $breadcrumb = new Breadcrumb();
        $breadcrumb->addCacheContexts(['route']);
        $links = [
            Link::createFromRoute(t('Home'), '<front>')
        ];

        if (!empty($breadcrumbItems)) {
            foreach ($breadcrumbItems as $id => $type) {
                switch ($type) {
                    // DrupRouter route
                    case 'drup_route':
                        if ($entity = $drupRouter->getEntity($id)) {
                            $links[] = Link::fromTextAndUrl($entity->getName(), $entity->toUrl());
                        }
                        break;

                    // entity_reference
                    case 'referenced_entity':
                        if ($entities = $drupField->getReferencedEntities($id)) {
                            ksort($entities);

                            if (($entity = current($entities)) && $entity !== null) {
                                $links[] = Link::fromTextAndUrl($entity->getName(), $entity->toUrl());
                            }
                        }
                        break;

                    // entity_reference taxonomy term with all parents
                    case 'referenced_taxonomy_term_parents':
                        if ($entities = $drupField->getReferencedEntities($id)) {
                            ksort($entities);

                            if (($entity = current($entities)) && $entity !== null && $termParents = \Drupal::service('entity_type.manager')->getStorage('taxonomy_term')->loadAllParents($entity->id())) {
                                ksort($termParents);
                                foreach ($termParents as $term) {
                                    /** @var Term $term **/
                                    if ($term->id() !== $this->drupPageEntity->id()) {
                                        $links[] = Link::fromTextAndUrl($term->getName(), $term->toUrl());
                                    }
                                }
                            }
                        }
                        break;
                }
            }
        }

        return $breadcrumb->setLinks($links)->addCacheableDependency(0);
    }
}
