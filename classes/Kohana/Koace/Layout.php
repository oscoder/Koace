<?php defined('SYSPATH') or die('No direct script access.');


class Kohana_Koace_Layout extends Kohana_Koace {

    /**
     * @var  string  partial name for content
     */
    const CONTENT_PARTIAL = 'content';

    /**
     * @var  string  layout path
     */
    protected $_layout = 'layout';

    public static function factory($layout = 'layout')
    {
        $k = parent::factory($layout);
        $k->set_layout($layout);
        return $k;
    }

    public function set_layout($layout)
    {
        $this->_layout = (string) $layout;
    }

    public function render($class, $template = NULL)
    {
        $content = $this->_engine
            ->getLoader()
            ->load($this->_detect_template_path($class));

        $this->_engine->setPartials(array(
            Kostache_Layout::CONTENT_PARTIAL => $content
        ));

        return $this->_engine->loadTemplate($this->_layout)->render($class);
    }

}
