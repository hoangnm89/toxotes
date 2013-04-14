<?php
namespace Flywheel\Router;
use Flywheel\Config\ConfigHandler as ConfigHandler;
use Flywheel\Base;
use Flywheel\Util\Inflection;

class WebRouter extends BaseRouter
{
    protected $_collectors = array();
    protected $_controllerPath;
    protected $_camelControllerName;
    protected $_controller;
    protected $_action;
    
    public $params = array();

    public function __construct() {
        $routes = ConfigHandler::load('app.config.routing', 'routing', true);
        //print_r($routes);exit;
        unset($routes['__urlSuffix__']);
        unset($routes['__remap__']);
        unset($routes['/']);

        if($routes and !empty($routes)){
            foreach ($routes as $pattern => $config){
                $this->_collectors[] = new Collection($config, $pattern);
            }
        }
        parent::__construct();
    }

    public function getPathInfo() {
        $pathInfo = parent::getPathInfo();
        if (ConfigHandler::has('routing.__urlSuffix__')
            && '' != ($suffix = ConfigHandler::get('routing.__urlSuffix__')))
            $pathInfo = str_replace($suffix, '', $pathInfo);
        return $pathInfo;
    }

    /**
     * Get Controller
     * 	ten controllers
     *
     * @return string
     */
    public function getController() {
        return $this->_controller;
    }

    /**
     * get controllers name after camelize
     *
     * @return string
     */
    public function getCamelControllerName() {
        return $this->_camelControllerName;
    }
    
    /**
     * get path of request controllers
     *
     * @return string
     */
    public function getControllerPath() {
        return $this->_controllerPath;
    }

    /**
     * Get Method
     *
     * @return string
     */
    public function getAction() {
        return $this->_action;
    }

    private function _parseDefaultController() {
        return ConfigHandler::get('routing./.route');
    }

    private function _parseControllers($route) {
        if (false === is_array($route)) {
            $route = explode('/', $route);
        }
        $_path = '';
        for ($i = 0; $i < sizeof($route); ++$i) {
            $_camelName	= Inflection::camelize($route[$i]);

            $_path .= $_camelName .DIRECTORY_SEPARATOR;
            if (false === (file_exists(Base::getAppPath().'/controllers/' .$_path))) {
                break;
            } else {
                $this->_camelControllerName = $_camelName;
                $this->_controllerPath		= $_path;
                $this->_controller = $route[$i];
            }
        }
        return $i;
    }

    public function parseUrl($url)
    {

        $config = ConfigHandler::get('routing');
        $rawUrl = $url;

        $url = $this->removeUrlSuffix($url, isset($config['__urlSuffix__'])? $config['__urlSuffix__']: null);

        if ('/' == $url) {
            if (!isset($config['/'])) { //default
                throw new \Flywheel\Exception\Routing('Router: Not found default "/" in config. Default must be set!');
            }
            $route = $this->_parseDefaultController();
        } else {

            for ($i = 0, $size = sizeof($this->_collectors); $i < $size; ++$i) {

                $route = $this->_collectors[$i]->parseUrl($this, trim($url, '/'), $rawUrl);

                if(false !== ($route = $this->_collectors[$i]->parseUrl($this, trim($url, '/'), $rawUrl)))
                    break;
            }
            if (false == $route)$route = trim($url, '/');
        }
        $segment = explode('/', $route);

        $seek = $this->_parseControllers($route);

        if (count($segment) > $seek) {
            $this->_action = $segment[$seek];
            $seek++;
            $this->params = array_slice($segment, $seek);
        }

        if (null == $this->_action) {
            $this->_action = 'default';
        }
    }
}
