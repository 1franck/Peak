<?php
/**
 * Simple lang translator based on php array
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */
class Peak_Lang
{	  
    private $_lang;
    private $_file;  
    public  $translations = array();


    /**
     * Call lang()
     *
     * @param string $lang
     */
	public function __construct($lang = 'en')
	{
	    $this->load($lang);
	}
	
	/**
     * Load lang translation files
     *
     * @param string $lang
     */
	public function load($lang = 'en')
	{
	    $this->_lang = trim(strtolower((string)$lang));	
		$this->_file = LANG_ABSPATH.'/'.$this->_lang.'.php';
		
		if(file_exists($this->_file)) {
		    $this->translations = include($this->_file);
		}
		elseif(file_exists(LANG_ABSPATH.'/en.php')) {
		    $this->translations = include(LANG_ABSPATH.'/en.php');
		}
		
		if(!is_array($this->translations)) $this->translations = array();
	}
	
	/**
	 * Translate text
	 *
	 * @param  string $item
	 * @param  string $replaces text replacements
     * @param  string $func callback function
	 * @return string
	 */
	public function translate($item)
	{		    
		$translation = (isset($this->translations[$item])) ? $this->translations[$item] : $item;

	    if(isset($replaces)) {
	        if(is_array($replaces)) $translation = vsprintf($result,$replaces);
	        else $translation = sprintf($result,$replaces);	        
	    }
	    
	    if(isset($func)) eval('$translation = '.$func.'(\''.$result.'\');');
	    
	    return $translation;
	}	

}


/**
 * Echo an translation
 *
 * @param see method translate() of Peak_Lang for info on params
 */
function __($text, $replaces = null, $func = null) {
		
	if(Peak_Registry::obj()->lang instanceof Peak_Lang)
	{       
	    return Peak_Registry::obj()->lang->translate((string)$text, $replaces, $func);
	}
	else return $text;
}

/**
 * Echo the result of __() function
 */
function _e($text,$replaces = null,$func = null) { echo __($text,$replaces,$func); }