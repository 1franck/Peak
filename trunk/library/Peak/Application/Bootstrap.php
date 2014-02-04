<?php
/**
 * Application Bootstrapper base
 *   
 * @author   Francois Lajoie
 * @version  $Id$
 */
abstract class Peak_Application_Bootstrap
{

    /**
     * init bootstrap
     */
    public function __construct()
    {
        $this->_configView();
        $this->_configRouter();
        $this->_boot();
    }

    /**
     * Call all bootstrap methods prefixed by "init"
     *
     * @param string $prefix
     */
    private function _boot($prefix = 'init')
    {
        $c_methods = get_class_methods(get_class($this));
        $l = strlen($prefix);
        foreach($c_methods as $m) {            
            if(substr($m, 0, $l) === $prefix) $this->$m();
        }
    }

    /**
     * Configure view from app config
     */
    protected function _configView()
    {

        if(!isset(Peak_Registry::o()->config->view) || 
            !Peak_Registry::isRegistered('view')) return;

        foreach(Peak_Registry::o()->config->view as $k => $v) {

            if(is_array($v)) {
                foreach($v as $p1 => $p2) Peak_Registry::o()->view->$k($p1,$p2);
            }
            else Peak_Registry::o()->view->$k($v);
        }
    }

    /**
     * Configure custom routes from app config
     */
    protected function _configRouter()
    {
        if(!isset(Peak_Registry::o()->config->router['addregex']) || 
            !Peak_Registry::isRegistered('router')) return;

        $r = Peak_Registry::o()->router;

        foreach(Peak_Registry::o()->config->router['addregex'] as $i => $exp) {
            $parts = explode(' | ', $exp);
            if(count($parts) == 2) {
                $r->addRegex(trim($parts[0]), trim($parts[1]));

            }
        }
    }
}