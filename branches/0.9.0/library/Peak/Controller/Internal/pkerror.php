<?php
/**
 * Internal controller for error, to use it you need to set Peak_Controller_Front::$allow_internal_controllers to true and
 * Peak_Controller_Front::$error_controller to 'pkerror'
 *
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_Controller_Internal_PkError extends Peak_Controller_Action
{
    
    /**
     * Initiate view
     */
    public function preAction()
    {
        $this->view->engine('VirtualLayouts');
        $this->view->disableCache();    
    }
    
    /**
     * Set layout after action
     */
    public function postAction()
    {
        $this->view->setLayout($this->_layout());
        //force render and exit script
        //$this->view->render('','');
        //exit();
    }   
    
    /**
     * Default action handler, support exception object
     */
    public function _index()
    {       
        if(isset($this->exception)) {
            if($this->exception instanceof Peak_Exception) {
            	switch($this->exception->getErrkey()) {
            	    
            	    case 'ERR_CTRL_NOT_FOUND' : 
            	    case 'ERR_ROUTER_URI_NOT_FOUND' :
            	        $this->_404();
            	        break;
            	        
            	    default: 
            	        $this->_500();
            	        break;    
            	}
            }
            else {
                $this->_error();
            }
        }
    }
    
    /**
     * Default error action
     */
    public function _error()
    {
        $this->view->title = 'Oh no! Something gone wrong';
        $this->view->title_desc = 'The page you are looking contains errors.';
        $this->devmode();
    }
    
    /**
     * 404 error action
     */
    public function _404()
    {
        $this->view->title = 'Oh no! Page not found!';
        $this->view->title_desc = '404 - The page you are looking for cant be found.';
        $this->devmode();
    }
    
    /**
     * 500 error action
     */
    public function _500()
    {
        $this->view->title = 'Internal Server Error';
        $this->view->title_desc = '500 - The server encountered an unexpected condition which prevented it from fulfilling the request.';
        $this->devmode();
    }
    
    /**
     * 503 error action
     */
    public function _503()
    {
        $this->view->title = 'Service Unavailable';
        $this->view->title_desc = '503 - The server is currently unavailable.';
        $this->devmode();
    }
    
    /**
     * Look for development environment and exception object infos
     */
    public function devmode()
    {
         if((APPLICATION_ENV === 'development') && (isset($this->exception))) {                            
            $this->view->setContent('<div class="block"><p>');
            $this->view->setContent($this->_exception2table());
            $this->view->setContent('</p></div>');
        }
    }
    
    /**
     * Tranforms array of exception infos into html table
     *
     * @return string
     */
    private function _exception2table()
    {
        $table = '<table>';
        $exception = array('Message' => $this->exception->getMessage(),
                           'Exception' => get_class($this->exception),
                           'File' => str_replace(array('\\',SVR_ABSPATH),array('/',''),$this->exception->getFile()),
                           'Line' => $this->exception->getLine(),
                           'Code' => $this->exception->getCode(),
                           'Trace' => str_replace('#','<br />#',$this->exception->getTraceAsString()));
                           
        $trace = explode('<br />',$exception['Trace']);
        $result = '';
        foreach($trace as $line) {
            $line_data = explode(' ',$line);
            if(count($line_data) >= 3) {
                foreach($line_data as $i => $col) {
					if(!isset($temp)) $temp = '';
                    if($i == 1 && !empty($col)) {
                        $temp = str_replace('):',')',$col);
                        $temp = explode('(',$temp);
						if(!is_array($temp)) $temp = '';
                        else $temp = str_replace(array('\\',SVR_ABSPATH),array('/',''),$temp[0]).'('.$temp[1];
                    }
                    elseif($i == 2) $result .= ' <strong><code>'.$col.'</code> --> </strong> '.$temp;
                    else $result .= ' '.$col;
                }
                $result .= '<br />';
            }
            else $result .= $line.'<br />';
        }
        $exception['Trace'] = $result;
                                  
        $exception['Time'] = (!method_exists($this->exception,'getTime')) ? date('Y-m-d H:i:s') : $this->exception->getTime();       
                           
        foreach($exception as $k => $v) {
            if($k === 'Message') $v = '<strong>'.$v.'</strong>';
            $table .= '<tr><td><strong>'.$k.'</strong>:&nbsp;</td><td>'.$v.'</td></tr>';
        }
        
        return $table.'</table>';
    }
    
    /**
     * Layout content
     *
     * @return string
     */
    private function _layout()
    {
        return '<!DOCTYPE html>
<html>
<head>
 <title>'.$this->view->title.'</title>
 <meta name="robots" content="noindex,nofollow" />
 <style type="text/css">
  <!--
 .pkerrbox table { margin:0; border: 0 !important; }
 .pkerrbox table td { padding:4px 8px; border: 0 !important; }
 .pkerrbox {
   margin:80px;
   font:12px "Verdana" !important;
   padding:15px 20px;
   border: 1px solid #8ec1da;
   background-color: #ddeef6;
   /*box-shadow: inset 0 1px 3px #fff, inset 0 -45px #cbe6f2, 0 0 3px #8ec1da;
   -o-box-shadow: inset 0 1px 3px #fff, inset 0 -45px #cbe6f2, 0 0 3px #8ec1da;
   -webkit-box-shadow: inset 0 1px 3px #fff, inset 0 -50% #cbe6f2, 0 0 3px #8ec1da;
   -moz-box-shadow: inset 0 1px 3px #fff, inset 0 -45px #cbe6f2, 0 0 3px #8ec1da;*/
   color: #3985a8;
   text-shadow: 0 1px #fff;
  }
 .pkerrbox.blue {
   border: 1px solid #8ec1da;
   background-color: #ddeef6;
   box-shadow: inset 0 1px 3px #fff, inset 0 -45px #cbe6f2, 0 0 3px #8ec1da;
   -o-box-shadow: inset 0 1px 3px #fff, inset 0 -45px #cbe6f2, 0 0 3px #8ec1da;
   -webkit-box-shadow: inset 0 1px 3px #fff, inset 0 -45px #cbe6f2, 0 0 3px #8ec1da;
   -moz-box-shadow: inset 0 1px 3px #fff, inset 0 -45px #cbe6f2, 0 0 3px #8ec1da;
   color: #3985a8;
 }
 .pkerrbox.yellow {
   border: 1px solid #EEE679;
   background-color: #F8F7C5;
   box-shadow: inset 0 1px 3px #fff, inset 0 -45px #EEE679, 0 0 3px #F8F7C5;
   -o-box-shadow: inset 0 1px 3px #fff, inset 0 -45px #EEE679, 0 0 3px #F8F7C5;
   -webkit-box-shadow: inset 0 1px 3px #fff, inset 0 -45px #EEE679, 0 0 3px #F8F7C5;
   -moz-box-shadow: inset 0 1px 3px #fff, inset 0 -45px #EEE679, 0 0 3px #F8F7C5;
   color: #99912B;
 }
 .pkerrbox.red {
   border: 1px solid #DE8D89;
   background-color: #F3C9C9;
   box-shadow: inset 0 1px 3px #fff, inset 0 -45px #E8B1AE, 0 0 3px #F3C9C9;
   -o-box-shadow: inset 0 1px 3px #fff, inset 0 -45px #E8B1AE, 0 0 3px #F3C9C9;
   -webkit-box-shadow: inset 0 1px 3px #fff, inset 0 -45px #E8B1AE, 0 0 3px #F3C9C9;
   -moz-box-shadow: inset 0 1px 3px #fff, inset 0 -45px #E8B1AE, 0 0 3px #F3C9C9;
   color: #A93630;
 }
 .pkerrbox.black {
   border: 1px solid #222;
   background-color: #444;
   box-shadow: inset 0 1px 3px #fff, inset 0 -45px #111, 0 0 3px #ccc;
   -o-box-shadow: inset 0 1px 3px #fff, inset 0 -45px #111, 0 0 3px #ccc;
   -webkit-box-shadow: inset 0 1px 3px #fff, inset 0 -45px #111, 0 0 3px #ccc;
   -moz-box-shadow: inset 0 1px 3px #fff, inset 0 -45px #111, 0 0 3px #ccc;
   color: #f1f1f1;
   text-shadow: 0 1px #111;
 }
 .pkerrbox h1 {
  font-size:22px;
  margin:-15px -15px 12px -15px;
  padding:10px 15px;
  border: 0 !important;
 }
 .pkerrbox h2 {
  font-weight:bold;
  font-size:14px;
  margin:0 0 0px 0;
  padding:0px 0 0 0px;
  border: 0 !important;
 }
 .pkerrbox .block {
  background:#f9f9f9;
  padding:12px;
  margin-top:15px;
  color:#111;
  text-shadow:none;
 }
 .pkerrbox .block p {
  padding:10px 5px;
  margin:0;
 }
 .pkerrbox, .pkerrbox .block {
  border-radius: 4px; -moz-border-radius: 4px; -o-border-radius:4px, -webkit-border-radius:4px;
 }
  -->
 </style>
</head>
<body>
<div class="pkerrbox blue">
<h1>'.$this->view->title.'</h1>
<h2>'.$this->view->title_desc.'</h2>
{CONTENT}
</div>
</body>
</html>';
    }
}