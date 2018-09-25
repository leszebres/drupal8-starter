<?php

namespace Drupal\drup;

use Drupal\Core\Template\Attribute;

/**
 * Class DrupViewsRows
 *
 * @package Drupal\drup
 */
class DrupViewsRows extends DrupViews {

    /**
     * DrupViewsRows constructor.
     *
     * @param $variables
     */
    public function __construct($variables) {
        parent::__construct($variables);

        // Thème
        $this->theme->controller = 'views_rows_controller';
        $this->theme->path .= '/partials';
    }

    /**
     * Defaults values
     */
    public function defaults() {
        // Theme
        if (!empty($this->variables['theme_hook'])) {
            $this->theme->hook = $this->theme->partial = $this->variables['theme_hook'];
        }
        $this->variables['theme'] = $this->theme;

        // Rows container (<ul>)
        $this->variables['rows_attributes'] = new Attribute();
        $this->variables['rows_attributes']->addClass('list');
    }
}
