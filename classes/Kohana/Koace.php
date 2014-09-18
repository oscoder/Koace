<?php defined('SYSPATH') or die('No direct access allowed.');

class Kohana_Koace {

    const VERSION = '1.0.0';

    protected $_engine;
    private $_renderClass;

    public static function factory($obj) {
        $m = new Mustache_Engine(
            array(
                'partials_loader' =>
                    new Mustache_Loader_KoaceLoader
                    (
                        array(
                            'layout' => $obj->layout_name,
                            'page' => $obj->page_name,
                            'aceui_dir' => $obj->aceui_dir
                        )
                    ),
                'escape' =>
                    function($value) {
                        return HTML::chars($value);
                    },
                'cache' => Kohana::$cache_dir.DIRECTORY_SEPARATOR.'mustache',
            )
        );

        $class = get_called_class();
        return new $class($m, $obj);
    }

    public function __construct($engine, $obj = NULL) {
        $this->_engine = $engine;
        $this->_renderClass = $obj;
    }

    public function render() {
        if ($this->_renderClass->template == NULL || $this->_renderClass->template == '') {
            $tpName = $this->_detect_template_path($this->_renderClass);
            $tpNata = $this->_engine->loadTemplate($tpName);
        } else {
            $tpNata = $this->_renderClass->template;
        }

        return $this->_engine->render($tpNata, $this->_renderClass->context);
    }

    public function engine() {
        return $this->_engine;
    }

    protected function _detect_template_path($class) {
        $path = explode('_', strtolower(get_class($class)));
        array_shift($path);

        return implode('.', $path);
    }
}