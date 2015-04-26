<?php
/**
 * Index Controller
 * 
 * Pass user request uri to index. All first level pages belong to views/script/index/ folder
 *
 * ex: http://mysite.com/my-url  => will show views/script/index/my-url.php if exists, otherwise 404
 *
 * @version $Id$
 */
class indexController extends Peak_Controller_Action
{

    /**
     * preAction() - Executed before controller handle any action
     */
    public function preAction()
    {
        $this->view->title = '';
    }

    /**
     * index Action
     */
    public function _index()
    {
        $r = Peak_Registry::o()->router;

        if(!empty($r->request_uri)) {

            $tpl = $this->getScriptsPath().'/'.$r->request_uri.'.php';

            if(file_exists($tpl)) {
                $this->file = basename($tpl);
                $this->view->title = $this->perma2text($r->request_uri);
            }
            else {
                $this->redirectAction('404');
                return;
            }
        }
    }

    /**
     * Page not found
     */
    public function _404()
    {
        $this->view->header()->setRCode(404);
    }

    /**
     * Transform 
     * 
     * @param  string $perma
     * @return string       
     */
    protected function perma2text($perma)
    {
        return ucfirst(str_ireplace('-', ' ', $perma));
    }
}