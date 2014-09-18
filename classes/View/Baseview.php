<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Created by PhpStorm.
 * User: oscoder
 * Date: 9/18/14
 * Time: 0:29
 */

class View_Baseview {
    public $layout_name;
    public $page_name = 'index';
    public $aceui_dir;

    public $template = '';
    public $context = array();


    public $paths = array();
    public $site;

    protected  function _load_file($base_dir, $name, $extension) {
        $filename = Kohana::find_file($base_dir, $name, $extension);

        if (! $filename) {
            throw new Kohana_Exception('Mustache template ":name" not found', array(':name' => $name));
        }

        return file_get_contents($filename);
    }

    public function __construct() {

        $this->aceui_dir = APPPATH.'/aceui/';
        $this->paths = array(
            'data' => 'data',
            'views' => 'views',
            'assets' => 'assets',
            'base' => 'aceui',
            'images' => 'assets/images'
        );
        $this->site = json_decode($this->_load_file(
            $this->paths['base'],
            $this->paths['data'].'/common/site',
            'json'));
        $this->site->protocol = '';//no protocol, so the page's default (http or https) will be used
        if($this->site->development == true) {
            $this->site->ace_scripts = array();
            $scripts = json_decode($this->_load_file(
                $this->paths['base'],
                $this->paths['assets'].'/js/ace/scripts',
                'json'
            ));

            foreach($scripts as $name => $include) {
                if($include) $this->site->ace_scripts[] = $name;
            }
        }

        //if no such page, then show 404 page!
        if(!is_file($this->paths['data']."/pages/{$this->page_name}.json")) {
            $page_name = "error-404";
        }

        $this->parseForView();
    }

    protected function parseForView() {
        $sidenav = new View_Sidenav();

        $page = new View_Page(
            array(
                'base' => $this->paths['base'],
                'path' => $this->paths,
                'name' => $this->page_name,
                'type' => 'page'
            )
        );

        $this->layout_name = $page->get_var('layout');
        $layout = new View_Page(
            array(
                'base' => $this->paths['base'],
                'path' => $this->paths,
                'name' => $this->layout_name,
                'type' => 'layout'
            )
        );

        if(($navList = &$layout->get_var('sidebar_items'))) {
            $sidenav->set_items($navList);
            $sidenav->mark_active_item($this->page_name);
        }

        $context =
            array(
                "page" => $page->get_vars(),
                "layout" => $layout->get_vars(),
                "path" => $this->paths,
                "site" => $this->site
            );

        $context['breadcrumbs'] = $sidenav->get_breadcrumbs();

        $context['createLinkFunction'] = function($value) {
            return '?page='.$value;
        };

        $this->template = $layout->get_template();
        $this->context = $context;
    }
} 