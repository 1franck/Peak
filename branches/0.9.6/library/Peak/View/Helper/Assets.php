<?php
/**
 * Assets url helpers for files like css/js/img
 *
 * @author  Francois Lajoie
 * @version $Id: assets.php 597 2013-03-04 18:16:01Z snake386@hotmail.com $
 */
class Peak_View_Helper_Assets
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
     * @param   string|null $path
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
     * @example ('css', array('theme/css/myfile1.css', ...)) will call method _asset_css() with the file(s) path(s)
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
        $mtype  = '_asset_'.$type;

        // force paths to be an array
        if(!is_array($paths)) $paths = array($paths);
        if(empty($paths)) return;

        // if asset type doesn't exists
        if(!method_exists($this, $mtype)) {

            // if type is auto, we will retreive asset based on file extension if asset method exists
            if(in_array($type, array('auto', 'auto-detect', 'autodetect'))) {

                foreach($paths as $p) {
                    $ext = '_asset_'.pathinfo($p, PATHINFO_EXTENSION);
                    if(method_exists($this, $ext)) $output .= $this->$ext($p);
                }

            }
            else return;
        }
        else {
            foreach($paths as $p) {
                $output .= $this->$mtype($p);
            }
        }

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