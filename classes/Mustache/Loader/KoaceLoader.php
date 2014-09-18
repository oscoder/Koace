<?php

class Mustache_Loader_KoaceLoader
    extends Mustache_Loader_FilesystemLoader
    implements Mustache_Loader, Mustache_Loader_MutableLoader
{
    private $_views_dir = 'aceui/views';
    private $_extension = 'mustache';
    private $_templates = array();

    public function __construct($options = array()) {
        if (isset($options['extension'])) {
            $this->_extension = ltrim($options['extension'], '.');
        }

        $this->_page_name = isset($options['page']) ? $options['page'] : 'index';
        $this->_layout_name = isset($options['layout']) ? $options['layout'] : 'default';

        $this->_views_dir = isset($options['aceui_dir'])
            ? $options['aceui_dir'].'/views'
            : APPPATH.'/aceui/views/';
        parent::__construct($this->_views_dir, $options);
    }

    public function load($name) {

        $parts = explode('.', $name, 2);
        $type = $parts[0];
        if ($type != 'page' AND $type != 'layout') {
            return '';
        }

        $file = str_replace('.', '/', $parts[1]);
        if($type == 'page' AND $file == 'content') {
            return parent::load("/{$type}s/{$this->_page_name}.mustache");
        }
        $item_name = '_'.$type.'_name';
        $item_name = $this->$item_name;

        $path = "/{$type}s/partials/{$item_name}/{$file}.mustache";
        if(!is_file($this->_views_dir.$path)) {
            //look in the upper folder, which contains partials for all pages or layouts
            $path = "/{$type}s/partials/_shared/{$file}.mustache";
            if(!is_file($this->_views_dir.$path)) {
                return '';
            }
        }
        return parent::load($path);
    }

    protected function _load_file($name) {
        $filename = Kohana::find_file('aceui', $name = strtolower($name), $this->_extension);

        if (!$filename) {
            throw new Kohana_Exception('Mustache template ":name" not found', array(':name' => $name));
        }

        return file_get_contents($filename);
    }

    /**
     * Set an associative array of Template sources for this loader.
     *
     * @param array $templates
     */
    public function setTemplates(array $templates) {
        $this->_templates = array_merge($this->_templates, $templates);
    }

    /**
     * Set a Template source by name.
     *
     * @param string $name
     * @param string $template Mustache Template source
     */
    public function setTemplate($name, $template) {
        $this->_templates[$name] = $template;
    }
}
