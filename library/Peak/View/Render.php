<?php
/**
 * Peak_View_Render Engine base
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */
abstract class Peak_View_Render
{
    
    public $scripts_file;          //controller action script view path used 
    public $scripts_path;          //controller action script view file name used
       
    protected $_cache;             //view cache object
           
    /**
     * Point to Peak_View __get method
     *
     * @param  string $name represent view var name
     * @return misc
     */
    public function __get($name)
    {
        return Peak_Registry::o()->view->$name;
    }
    
    /**
     * Point to Peak_View __isset method
     *
     * @param  string $name represent view var name
     * @return bool
     */
    public function __isset($name)
    {
        return isset(Peak_Registry::o()->view->$name);
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
    	$view =& Peak_Registry::o()->view;
    	return call_user_func_array(array($view, $method), $args);
    }

    
    /**
     * Return public root url of your application
     *
     * @param  string $path Add custom paths/files to the end
     * @return string
     */
    public function baseUrl($path = null, $return = false)
    {
    	if(defined('PUBLIC_URL')) $url = PUBLIC_URL.'/'.$path;
    	elseif(isset($_SERVER['SERVER_NAME'])) {
    		$url = 'http://'.$_SERVER['SERVER_NAME'].'/'.PUBLIC_ROOT.'/'.$path;
    	}
    	//remove double slash(//) inside url
    	$url_part = explode('http://', $url);
    	$url = 'http://'.str_replace(array('///','//'),'/',$url_part[1]);
    	
    	if(!$return) echo $url;  
    	else return $url;
    }


    /**
     * Call child output method and cache it if cache activated;
     * Can be overloaded by engines to customize how the cache data
     *
     * @param misc $data
     */
    protected function preOutput($data)
    {
        if(!$this->isCacheActivated()) $this->output($data);
        else {            
            //use cache instead outputing and evaluating view script
            if($this->cache()->isCached()) include($this->cache()->getCacheFile());
            else {
                //cache and output current view script
                ob_start();
                $this->output($data);
                //if(is_writable($cache_file)) { //fail if file cache doesn't already 
                    $content = ob_get_contents();
                    //if($this->_cache_strip) $content = preg_replace('!\s+!', ' ', $content);
                    file_put_contents($this->cache()->getCacheFile(), $content);
                //}
                ob_get_flush();
            }           
        }        
    }
    
    /**
     * Access to cache object
     *
     * @return object Peak_View_Cache
     */
    public function cache()
    {
    	if(!is_object($this->_cache)) $this->_cache = new Peak_View_Cache();
    	return $this->_cache;
    }
    
    /**
     * Check if cache object is loaded and cache enable
     *
     * @return bool
     */
    public function isCacheActivated()
    {
    	if(!is_object($this->_cache)) return false;
    	else return $this->cache()->isEnabled();
    }
    
    /**
     * Shorcut of $this->cache()->enable()
     *
     * @see Peak_View_Cache::enable()
     */
    public function enableCache($time)
    {
    	$this->cache()->enable($time);
    }
    
    /**
     * Shorcut of $this->cache()->disable()
     */
    public function disableCache()
    {
    	$this->cache()->disable();	
    }
    
    /**
     * Shorcut of $this->cache()->isCached()
     *
     * @see Peak_View_Cache::isCached()
     */
    public function isCached($id = null)
    {
    	return $this->cache()->isCached($id);
    }

    /**
     * Shorcut of $this->cache()->isCachedBlock()
     *
     * @see Peak_View_Cache::isCachedBlock()
     */
    public function isCachedBlock($id, $expiration)
    {
    	return $this->cache()->isCachedBlock($id, $expiration);
    }

    /**
     * Shorcut of $this->cache()->cacheBlockEnd()
     */
    public function cacheBlockEnd()
    {
    	$this->cache()->cacheBlockEnd();
    }

    /**
     * Shorcut of $this->cache()->getCacheBlock()
     */
    public function getCacheBlock()
    {
    	$this->cache()->getCacheBlock();
    }      
}