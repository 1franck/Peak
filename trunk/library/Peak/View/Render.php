<?php

/**
 * Peak_View_Render Engine base
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */
abstract class Peak_View_Render
{
    
    protected $_scripts_file;          //controller action script view path used 
    protected $_scripts_path;          //controller action script view file name used
    
    protected $_use_cache = false;     //use scripts view cache, false by default
    protected $_cache_expire;          //script cache expiration time
    protected $_cache_path;            //scripts view cache path. generate by enableCache()
    protected $_cache_id;              //current script view md5 key. generate by preOutput() 
    protected $_cache_strip = false;   //will strip all repeating space caracters
    
    /**
     * Point to Peak_View __get method
     *
     * @param  string $name represent view var name
     * @return misc
     */
    public function __get($name)
    {
        return Peak_Registry::obj()->view->$name;
    }
    
    /**
     * Silent call to unknow method or
     * Throw trigger error when DEV_MODE is activated 
     * 
     * @param string $method
     * @param array  $args
     */
    public function  __call($method, $args)
    {
        if((defined('DEV_MODE')) && (DEV_MODE)) {
            trigger_error('DEV_MODE: View Render method '.$method.'() doesn\'t exists');
        }
    }
    
    /**
     * Return view helpers object from Peak_View::helper()
     *
     * @param  string $name
     * @return object
     */
    public function helper($name = null)
    {
        return Peak_Registry::obj()->view->helper($name);
    }
    
    /**
     * Return public root url of your application
     *
     * @param  string $path Add custom paths/files to the end
     * @return string
     */
    public function baseUrl($path = null)
    {
    	if(defined('ROOT_URL')) return ROOT_URL.'/'.$path;
    	elseif(isset($_SERVER['DOCUMENT_ROOT'])) {
    		return $_SERVER['DOCUMENT_ROOT'].'/'.$path;
    	}
    }
    
    
    /**
     * Enable output caching. 
     * Avoid using in controllers actions that depends on $_GET, $_POST or any dynamic value for setting the view
     *
     * @param integer $time set cache expiration time(in seconds)
     */
    public function enableCache($time)
    {
        if(is_integer($time)) {
            $this->_use_cache = true;
            $this->_cache_expire = $time;
            $this->_cache_path = Peak_Core::getPath('theme_cache');
        }
    }
    
    /**
     * Desactivate output cache
     */
    public function disableCache()
    {
        $this->_use_cache = false;
    }
    
    /**
     * Call child output method and cache it if $_use_cache is true;
     * Can be overloaded by engines to customize how the cache data
     *
     * @param misc $data
     */
    protected function preOutput($data)
    {
        if(!$this->_use_cache) $this->output($data);
        else {         
            //generate script view cache id
            $this->genCacheId();
            
            //use cache instead outputing and evaluating view script
            if($this->isCached()) include($this->getCacheFile());
            else {
                //cache and output current view script
                ob_start();
                $this->output($data);
                //if(is_writable($cache_file)) { //fail if file cache doesn't already 
                    $content = ob_get_contents();
                    if($this->_cache_strip) $content = preg_replace('!\s+!', ' ', $content);
                    file_put_contents($this->getCacheFile(), $content);
                //}
                ob_get_flush();
            }           
        }        
    }
    
    /**
     * Check if current view script file is cached/expired
     * Note: if $this->_cache_id is not set, this will generate a new id from $id params if set or from the current controller file - path
     *
     * @return bool
     */
    public function isCached($id = null)
    {
        if(!$this->_use_cache) return false;   
        
        //when checking isCached in controller action. $_scripts_file, $_scripts_path, $_cache_id are not set yet
        if(!isset($this->_cache_id)) {
            if(!isset($id)) {
                $this->genCacheId(Peak_Registry::obj()->app->controller->path, Peak_Registry::obj()->app->controller->file);
            }
            else $this->genCacheId('', $id);
        }
        
        $filepath = $this->getCacheFile();
            
        if(file_exists($this->getCacheFile())) {
            $file_date = filemtime($this->getCacheFile());
            $now = time();
            $delay = $now - $file_date;
            return ($delay >= $this->_cache_expire) ? false : true;
        }
        else return false;
    }
    
    /**
     * Generate md5 cache id from script view filename and path by default
     * Set a $path and $file to generate a new custom id.
     *
     * @param string $path
     * @param string $file
     */
    protected function genCacheId($path = null,$file = null)
    {
        //use current $this->_script_file and _script_path if no path/file scpecified
        if(!isset($path))  $key = $this->_scripts_path.$this->_scripts_file;
        else $key = $this->_cache_id = $path.$file;

        $this->_cache_id = hash('md5', $key);
    }
    
    /**
     * Get current cached view script complete filepath
     *
     * @return string
     */
    protected function getCacheFile()
    {
        return $this->_cache_path.'/'.$this->_cache_id.'.php';
    }
    
    /**
     * Enable/disable cache compression
     *
     * @param bool $status
     */
    public function enableCacheStrip($status)
    {
    	if(is_bool($status)) $this->_cache_strip = $status;
    }
    
    /**
     * Allow caching block inside views
     * Check if a custom cache block is expired
     *
     * @param  string $id
     * @param  integer $expiration
     * @return bool
     */
    protected function isCachedBlock($id, $expiration)
    {
        $this->enableCache($expiration);
        if($this->isCached($id)) return true;
        else {
            ob_start();
            return false;
        }
    }
    
    /**
     * Close buffer of a cache block previously started by isCachedBlock()
     */
    protected function cacheBlockEnd()
    {
        file_put_contents($this->getCacheFile(), preg_replace('!\s+!', ' ', ob_get_contents()));
        ob_get_flush();
    }
    
    /**
     * Get a custom cache block
     */
    protected function getCacheBlock()
    {
        include($this->getCacheFile());
    }
        
}