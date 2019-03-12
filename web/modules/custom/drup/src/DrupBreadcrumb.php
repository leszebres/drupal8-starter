<?php

/**
 * @file
 * Contains Drupal\mymodule\MyModuleBreadcrumbBuilder.
 */

namespace Drupal\drup;

use Drupal\Core\Breadcrumb\Breadcrumb;
use Drupal\Core\Breadcrumb\BreadcrumbBuilderInterface;
use Drupal\Core\Link;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\drup\Entity\ContentEntityBase;
use Drupal\drup\Helper\DrupRequest;
use Drupal\Core\Url;
use Drupal\drup\Entity\DrupField;

/**
 * Class MyModuleBreadcrumbBuilder.
 *
 * @package Drupal\mymodule
 */
class DrupBreadcrumb implements BreadcrumbBuilderInterface {

    /**
     * @var
     */
    protected $entity;

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
            $this->entity = DrupPageEntity::loadEntity();
            $breadcrumbItems = $this->buildList();

            if (!empty($this->entity->getBundle()) && isset($breadcrumbItems[$this->entity->getEntityType()]) && array_key_exists($this->entity->getBundle(), $breadcrumbItems[$this->entity->getEntityType()])) {
                return true;
            }
        }

        return false;
    }

    /**
     * todo revoir toutes les méthodes
     *
     * @see http://kevinquillen.com/drupal/2017/02/16/manually-add-breadcrumb-links-in-drupal-8
     * {@inheritdoc}
     */
    public function build(RouteMatchInterface $route_match) {
        $drupRouter = \Drupal::service('drup_router.router');

        $breadcrumb = new Breadcrumb();
        $breadcrumb->addCacheContexts(['route']);
        $links = [];

        $breadcrumbItemsList = $this->buildList();
        $breadcrumbItems = $breadcrumbItemsList[$this->entity->type][$this->entity->bundle];

        $drupField = new DrupField($this->entity->entity);

        $links[] = Link::createFromRoute(t('Home'), '<front>');

        if (!empty($breadcrumbItems)) {
            foreach ($breadcrumbItems as $id => $type) {
                switch ($type) {
                    case 'druproute':
                        $node = current(ContentEntityBase::getReferencedNodes([['target_id' => $drupRouter->getId($id)]]));
                        if (is_object($node)) {
                            $links[] = Link::fromTextAndUrl($node->name, Url::fromUri($node->uri));
                        }
                        break;

                    case 'taxonomy_term':
                        if ($terms = $drupField->getValues($id)) {
                            $term = current(ContentEntityBase::getReferencedTerms($terms));
                            if (is_object($term)) {
                                if ($termParents = \Drupal::service('entity_type.manager')->getStorage('taxonomy_term')->loadAllParents($term->id)) {
                                    ksort($termParents);
                                    foreach ($termParents as $term) {
                                        if ($term->id() !== $this->entity->id) {
                                            $termTarget = ['target_id' => $term->id()];
                                            $term = current(ContentEntityBase::getReferencedTerms([$termTarget]));
                                            $links[] = Link::fromTextAndUrl($term->name, Url::fromUri($term->uri));
                                        }
                                    }
                                } else {
                                    $links[] = Link::fromTextAndUrl($term->name, Url::fromUri($term->uri));
                                }
                            }
                        }
                        break;

                    case 'taxonomy_term_parents':
                        if ($termParents = \Drupal::service('entity_type.manager')->getStorage('taxonomy_term')->loadAllParents($this->entity->id)) {
                            ksort($termParents);
                            foreach ($termParents as $term) {
                                if ($term->id() !== $this->entity->id) {
                                    $termTarget = ['target_id' => $term->id()];
                                    $term = current(ContentEntityBase::getReferencedTerms([$termTarget]));
                                    $links[] = Link::fromTextAndUrl($term->name, Url::fromUri($term->uri));
                                }
                            }
                        }
                        break;

                    case 'node':
                        if ($nodes = $drupField->getValues($id)) {
                            $node = current(ContentEntityBase::getReferencedNodes($nodes));
                            if (is_object($node)) {
                                $links[] = Link::fromTextAndUrl($node->name, Url::fromUri($node->uri));
                            }
                        }
                        break;
                }
            }
        }

        return $breadcrumb->setLinks($links)->addCacheableDependency(0);
    }
}
