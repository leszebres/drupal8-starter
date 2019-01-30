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
use Drupal\node\Entity\Node;
use Drupal\Core\Url;

/**
 * Class MyModuleBreadcrumbBuilder.
 *
 * @package Drupal\mymodule
 */
class DrupBreadcrumbBuilder implements BreadcrumbBuilderInterface {

    /**
     * @var
     */
    protected $entity;

    /**
     * Custom breadcrumb items for nodes or terms
     * [EntityType (node, term)] => [Bundle (node type or vocabulary name)] => [ID/FieldID => TYPE]
     * @return array
     */
    public function getCustomBreadcrumbItemsList() {
        $breadcrumbs = [
            'node' => [
//                'healthreview_article' => [
//                    'health-review' => 'druproute',
//                    'thematics' => 'taxonomy_term',
//                    'node_file' => 'node'
//                ],
            ],
            'taxonomy_term' => [
            ]
        ];
        
        return $breadcrumbs;
    }
    
    /**
     * {@inheritdoc}
     */
    public function applies(RouteMatchInterface $route_match) {
        if (!DrupCommon::isAdminRoute()) {
            $this->entity = DrupCommon::getPageEntity(true);
            $breadcrumbItems = $this->getCustomBreadcrumbItemsList();

            if (!empty($this->entity->bundle) && isset($breadcrumbItems[$this->entity->type]) && array_key_exists($this->entity->bundle, $breadcrumbItems[$this->entity->type])) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * @see http://kevinquillen.com/drupal/2017/02/16/manually-add-breadcrumb-links-in-drupal-8
     * {@inheritdoc}
     */
    public function build(RouteMatchInterface $route_match) {
        $drupRouter = \Drupal::service('drup_router.router');
    
        $breadcrumb = new Breadcrumb();
        $breadcrumb->addCacheContexts(['route']);
        $links = [];
        
        $breadcrumbItemsList = $this->getCustomBreadcrumbItemsList();
        $currentEntity = DrupCommon::getPageEntity(true);
        $breadcrumbItems = $breadcrumbItemsList[$currentEntity->type][$currentEntity->bundle];
        
        $drupField = new DrupEntityField($currentEntity->entity);
        
        $links[] = Link::createFromRoute(t('Home'), '<front>');
        
        if (!empty($breadcrumbItems)) {
            foreach ($breadcrumbItems as $id => $type) {
                switch ($type) {
                    case 'druproute':
                        $nid = $drupRouter->getId($id);
                        if ($node = Node::load($nid)) {
                            $links[] = Link::fromTextAndUrl($node->getTitle(), Url::fromRoute('entity.node.canonical', ['node' => $nid]));
                        }
                        break;
                    case 'taxonomy_term':
                        if ($terms = $drupField->getValues($id)) {
                            $term = current(DrupCommon::getReferencedTerms($terms));
                            if (is_object($term)) {
                                $links[] = Link::fromTextAndUrl($term->name, Url::fromUri($term->uri));
                            }
                        }
                        break;
                    case 'taxonomy_term_parents':
                        if ($termParents = \Drupal::service('entity_type.manager')->getStorage('taxonomy_term')->loadAllParents($currentEntity->id)) {
                            ksort($termParents);
                            foreach ($termParents as $term) {
                                if ($term->id() !== $currentEntity->id) {
                                    $termTarget = ['target_id' => $term->id()];
                                    $term = current(DrupCommon::getReferencedTerms([$termTarget]));
                                    $links[] = Link::fromTextAndUrl($term->name, Url::fromUri($term->uri));
                                }
                            }
                        }
                        break;
                    case 'node':
                        if ($nodes = $drupField->getValues($id)) {
                            $node = current(DrupCommon::getReferencedNodes($nodes));
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
