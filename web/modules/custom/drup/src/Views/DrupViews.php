<?php

namespace Drupal\drup\Views;

use Drupal\Core\Template\Attribute;
use Drupal\views\ViewExecutable;
use Drupal\views\Views;

/**
 * Class DrupViews
 *
 * @package Drupal\drup
 */
class DrupViews {

    /**
     * @var
     */
    protected $variables;

    /**
     * @var \Drupal\Core\Theme\ActiveTheme
     */
    protected $activeTheme;

    /**
     * @var \StdClass
     */
    protected $theme;

    /**
     * @var \StdClass
     */
    protected $view;

    /**
     * DrupViews constructor.
     *
     * @param $variables
     */
    public function __construct($variables) {
        $this->variables = $variables;

        // Theme
        $this->activeTheme = \Drupal::theme()->getActiveTheme();
        $this->theme = new \StdClass;
        $this->theme->name = $this->activeTheme->getName();
        $this->theme->path = '/' . $this->activeTheme->getPath() . '/templates';

        // View
        $this->view = new \StdClass;
        $this->view->id = $this->variables['view']->id();
        $this->view->displayId = $this->variables['view']->current_display;
    }

    /**
     * @param $viewId
     * @param $viewDisplayId
     * @param array $arguments
     * @param bool $render
     *
     * @return mixed
     */
    public static function buildView($viewId, $viewDisplayId, $arguments = [], $render = true)
    {
        $view = Views::getView($viewId);

        if ($view instanceof ViewExecutable) {
            $view->setDisplay($viewDisplayId);

            if (!empty($arguments)) {
                $arguments = [implode(',', $arguments)];
                $view->setArguments($arguments);
            }

            // Render
            if ($render) {
                $view->execute();

                if (!empty($view->result)) {
                    $rendered = $view->render();
                    return \Drupal::service('renderer')->render($rendered);
                }
            }

            // Or get results
            $view->preview();
            if (!empty($view->result)) {
                return $view->result;
            }

        }

        return null;
    }

    /**
     * Defaults values
     */
    public function defaults() {
        // Theme
        $this->variables['theme'] = $this->theme;

        // Rows container (<ul>)
        $this->variables['rows_attributes'] = new Attribute();
        $this->variables['rows_attributes']->addClass('list');
    }

    /**
     * @return bool|mixed
     */
    public function controller() {
        if (isset($this->theme->controller)) {
            // Default
            $this->defaults();
            $suggestions = [
                $this->theme->name . '_' . $this->theme->controller
            ];

            // Suggestion custom
            if (isset($this->theme->hook)) {
                $suggestions[] = $this->theme->name . '_' . $this->theme->controller . '__' . $this->theme->hook;
            }

            // ViewId and/or viewId+DisplayId
            $suggestions[] = $this->theme->name . '_' . $this->theme->controller . '__' . $this->view->id;
            $suggestions[] = $this->theme->name . '_' . $this->theme->controller . '__' . $this->view->id . '__' . $this->view->displayId;

            // Call suggestions
            foreach ($suggestions as $suggestion) {
                if (function_exists($suggestion)) {
                    $this->variables = call_user_func($suggestion, $this->variables);
                }
            }

            return $this->variables;
        }

        return false;
    }
}
