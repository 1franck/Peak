<?php
/**
 * Assets url helpers for files like css/js/img
 *
 * @author  Francois Lajoie
 * @version $Id$
 */
class View_Helper_assets
{

    /**
     * Assets end url path
     * @var string
     */
    private $_assets_path;

    /**
     * Assets base url.
     * @var string
     */
    private $_assets_base_url = null;

    /**
     * Init the class and set default assets path and base url optionnaly
     * 
     * @param string|null $path
     */
    public function __construct($path = null, $url = null)
    {
        if(isset($path)) $this->setPath($path);
        else $this->setPath('assets');

        if(isset($url)) $this->setUrl($url);
        else $this->setUrl(Peak_Registry::o()->view->baseUrl('', true));
    }

    /**
     * Delegate type method/args to process()
     * 
     * @param  string $method   
     * @param  string $args 
     * @return string       
     */
    public function __call($method, $args)
    {
        return $this->process($method, $args[0]);
    }

    /**
     * Set assets path
     * 
     * @param string $path
     */
    public function setPath($path)
    {
        $this->_assets_path = $path;
        return $this;
    }

    /**
     * Set assets base url
     * 
     * @param string $url
     */
    public function setUrl($url)
    {
        if(substr($url, -1, 1) === '/') $url = substr($url, 0, strlen($url) - 1);
        $this->_assets_base_url = $url;

        return $this;
    }

    /**
     * Proccess a single or a bunch of assets file
     *
     * @param  string        $type
     * @param  array|string  $paths
     *
     * @return string
     */
    public function process($type, $paths)
    {
        $output = '';
        $type   = '_asset_'.$type;

        if(!method_exists($this, $type)) return;

        if(is_array($paths) && !empty($paths)) {
            foreach($paths as $p) {
                $output .= $this->$type($p);
            }
        }
        else $output = $this->$type($paths);

        return $output;
    }

    /**
     * The url for the assets. If no assets url specified, we use baseUrl()
     *
     * @param  string $filepath
     *
     * @return string
     */
    protected function _asset_url($filepath)
    {
        $url = $this->_assets_base_url.'/'.$this->_assets_path.'/'.$filepath;

        return $url;
    }


    /**
     * Javascript <script> tag
     *
     * @param  string $path
     *
     * @return string
     */
    protected function _asset_js($filepath)
    {
        return '<script type="text/javascript" src="'.$this->_asset_url($filepath).'"></script>';
    }

    /**
     * Stylesheet <link> tag
     *
     * @param  string $path
     *
     * @return string
     */
    protected function _asset_css($filepath)
    {
        return '<link rel="stylesheet" href="'.$this->_asset_url($filepath).'">';
    }

}