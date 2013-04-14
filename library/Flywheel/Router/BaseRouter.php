<?php
namespace Flywheel\Router;
use Flywheel\Config\ConfigHandler as ConfigHandler;
abstract class BaseRouter extends \Flywheel\Object
{
    public static $methods = array('GET', 'POST', 'PUT', 'DELETE', 'HEAD');
    protected $_params = array();
    protected $_routes = array();
    protected $_domain;
    protected $_baseUrl;
    protected $_uri;
    protected $_url;
    public $config;

    protected $_camelControllerName;
    protected $_controllerPath;
    protected $_controller;

    public function __construct() {
        $this->_url = $this->getPathInfo();
        $this->_domain = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on')? 'https://':'http://') .@$_SERVER['HTTP_HOST'];
        $this->_baseUrl = $this->_domain .str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
        if (isset($_SERVER['SCRIPT_NAME']) && $pos = strripos($this->_url, basename($_SERVER['SCRIPT_NAME'])) !== false)
            $this->_baseUrl = substr($this->_baseUrl, 0, $pos);

        $this->_uri = $this->_domain.@$_SERVER['REQUEST_URI'];

        $this->parseUrl($this->_url);
    }

    public function getBaseUrl()
    {
        return $this->_baseUrl;
    }

    public function getDomain()
    {
        return $this->_domain;
    }

    public function getUrl()
    {
        return $this->_url;
    }

    public function getUri()
    {
        return $this->_uri;
    }

    /**
     * Creates a path info based on the given parameters.
     * @param array $params list of GET parameters
     * @param string $equal the separator between name and value
     * @param string $ampersand the separator between name-value pairs
     * @param string $key this is used internally.
     * @return string the created path info
     */
    public function createPathInfo($params,$equal,$ampersand, $key=null)
    {
        $pairs = array();
        foreach($params as $k => $v)
        {
            if ($key!==null)
                $k = $key.'['.$k.']';

            if (is_array($v))
                $pairs[]=$this->createPathInfo($v,$equal,$ampersand, $k);
            else
                $pairs[]=urlencode($k).$equal.urlencode($v);
        }
        return implode($ampersand,$pairs);
    }

    /**
     * Parses a path info into URL segments and saves them to $_GET and $_REQUEST.
     * @param string $pathInfo path info
     */
    public function parsePathInfo($pathInfo)
    {
        if($pathInfo==='')
            return;
        $segs=explode('/',$pathInfo.'/');
        $n=count($segs);
        for($i=0;$i<$n-1;$i+=2)
        {
            $key=$segs[$i];
            if($key==='') continue;
            $value=$segs[$i+1];
            if(($pos=strpos($key,'['))!==false && ($m=preg_match_all('/\[(.*?)\]/',$key,$matches))>0)
            {
                $name=substr($key,0,$pos);
                for($j=$m-1;$j>=0;--$j)
                {
                    if($matches[1][$j]==='')
                        $value=array($value);
                    else
                        $value=array($matches[1][$j]=>$value);
                }
                if(isset($_GET[$name]) && is_array($_GET[$name]))
                    $value=array_merge($_GET[$name],$value);
                $this->_params[$name] = $_GET[$name] = $value;
            }
            else
                $this->_params[$key] = $_GET[$key]=$value;
        }
    }

    /**
     * Get Path Info
     *
     * @return string url
     */
    public function getPathInfo() {
        if (isset($_SERVER['PATH_INFO']) && ($_SERVER['PATH_INFO'] != '')) {
            $pathInfo = $_SERVER['PATH_INFO'];
        }
        else {
            $pathInfo = preg_replace('/^'.preg_quote($_SERVER['SCRIPT_NAME'], '/').'/', '', @$_SERVER['REQUEST_URI']);
            $pathInfo = preg_replace('/^'.preg_quote(preg_replace('#/[^/]+$#', '', $_SERVER['SCRIPT_NAME']), '/').'/', '', $pathInfo);
            $pathInfo = preg_replace('/\??'.preg_quote(@$_SERVER['QUERY_STRING'], '/').'$/', '', $pathInfo);
            if ($pathInfo == '') $pathInfo = '/';
        }
        return $pathInfo;
    }

    abstract public function parseUrl($url);
    /**
     * Removes the URL suffix from path info.
     * @param string $pathInfo path info part in the URL
     * @param string $urlSuffix the URL suffix to be removed
     * @return string path info with URL suffix removed.
     */
    public function removeUrlSuffix($pathInfo,$urlSuffix = null)
    {
        if(null != $urlSuffix && substr($pathInfo,-strlen($urlSuffix))===$urlSuffix)
            return substr($pathInfo,0,-strlen($urlSuffix));
        else
            return $pathInfo;
    }

    public function getParams() {
        return $this->_params;
    }

    public function getParamIndex($index)
    {
        return (isset($this->_params[$index]))? $this->_params[$index] : null;
    }
}
