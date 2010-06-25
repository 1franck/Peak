<?php

/**
 * Peak_Core_Lang object extension 
 *
 */
class Peak_Core_Lang
{
    
    /**
     * Get valid language folders available
     * 
     * @example /lang/en/main.php is valid
     *
     * @return array
     */
    public function get()
    {        
        $wlang = array();
        
        //list language directory
        try {
            $it = new DirectoryIterator(Peak_Core::getPath('lang'));

            while($it->valid()) {
                if(($it->isDir()) && (!in_array($it->getFilename(),array('.','..')))) {
                    $wlangfile = Peak_Core::getPath('lang').'/'.$it->getFilename().'.php';
                    if(file_exists($wlangfile)) $wlang[] = $it->getFilename();       
                }
                $it->next();
            }           
            return $wlang;
        }
        catch(Exception $e) { $this->w_errors[] = $e->getMessage(); return false; }
    }
    
    
}