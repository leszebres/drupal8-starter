<?php

namespace Drupal\drup;

use Drupal\Core\Template\Attribute;

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
    protected $theme;
    protected $view;
    
    /**
     * DrupViews constructor.
     *
     * @param $variables
     */
    public function __construct($variables) {
        $this->variables = $variables;
        
        // Thème
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
     * Execute Controller
     *
     * @return array
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
