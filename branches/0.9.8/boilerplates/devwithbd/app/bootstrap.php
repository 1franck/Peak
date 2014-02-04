<?php
/**
 * App Bootstrapper
 *
 * @version $Id$
 */
class Bootstrap extends Peak_Application_Bootstrap
{

    /**
     * Init Custom routes
     */
    public function _initRoutes()
    {
    }

    /**
     * Init database
     * @uses Zend_Db component
     */
    public function _initDb()
    {
        $dbconfig = Peak_Registry::o()->config->db;
        $db = Zend_Db::factory( $dbconfig['adapter'], $dbconfig['params'] );
        Zend_Db_Table::setDefaultAdapter($db);
    }

}