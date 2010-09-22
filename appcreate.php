<?php

/**
 * Peak Framework Application Creator
 * 
 * @descr   This is a all-in-one file. This file assumed that Peak Framework is placed in this folder under /library/Peak/ as by default, 
 *          if Peak is somewhere else, edit $pk_path to point to Peak library folder.
 *          ! This file SHOULD BE DELETED/NOT USED in PRODUCTION environment.
 * 
 * @uses    Peak_Core, Peak_Dispatcher, Jquery (http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js)
 *          
 * @author  Francois Lajoie
 * @version $Id$
 */

error_reporting(E_ALL | E_STRICT);

//EDIT THIS IF PEAK FRAMEWORK FOLDER IS NOT IN THE SAME LOCATION AS THIS FILE.
$pk_path = dirname(__FILE__).'/library/Peak';


/* STOP EDITING */

$pk_path = str_replace('\\','/',$pk_path);

//current file absolute path
$abspath = str_replace('\\', '/', realpath(dirname(__FILE__)).'\\');

$document_root = $_SERVER['DOCUMENT_ROOT'];

//check php version
if(version_compare(PHP_VERSION, '5.2.0', '<=')) {
    _die('You need at least PHP 5.2 to use Peak Framework. You current version is PHP '.PHP_VERSION);
}


//check $pk_path
if(!is_file($pk_path.'/Core.php')) {
	_die('We are unable to find Peak Framework folder under '.$pk_path.'.<br /> Edit $pk_path from this file to change this.');
}

/**
 * INCLUDES PEAK FILE
 */

include $pk_path.'/Dispatcher.php';

/**
 * CLASS
 */
class appcreate extends Peak_Dispatcher
{
    
    /**
     * Return base64 decoded img header
     * @trigger $_GET['img']
     */
    public function _GET_img()
    {
        global $img;
        //show img
        if(isset($_GET['img']))
        {
            if(isset($img[$_GET['img']])) {
                header("Content-type: image/png");
                echo base64_decode($img[$_GET['img']]);
                exit();
            }
        }
    }
    
    /**
     * phpinfo page
     */
    public function _GET_phpinfo()
    {
        phpinfo(); exit();
    }
    
    /**
     * Validate new application form
     * @trigger $_POST['submit']
     */
    public function _POST_submit()
    {        
    	global $pk_path, $abspath, $document_root;
    	
        $warnings = array();
        $this->response['submit_pass'] = false;
                
        //check if we got valid values
        
        //app name
        if(empty($this->resource['app_name'])) { $warnings['app_name'] = 'Application name missing!'; }
        elseif(!preg_match('/^[a-zA-Z0-9]*$/',$this->resource['app_name'])) {
            $warnings['app_name'] = 'Application name contains spaces and/or invalid characters';
        }
        elseif(is_dir($this->resource['app_path'].'\\'.$this->resource['app_name'])) {
            $warnings['app_name'] = 'Application name folder already exists';
        }
        
        //app path
        if(empty($this->resource['app_path'])) { $warnings['app_path'] = 'Application path missing!'; }
        elseif(!is_dir($this->resource['app_path'])) {
            $warnings['app_path'] = 'Application path not found';
        }
        /*
        //library_path
        if(empty($this->resource['library_path'])) { $warnings['library_path'] = 'Library path missing'; }
        elseif(!is_dir($this->resource['library_path'].'/Peak')) {
            $warnings['library_path'] = '/Peak/ folder no found in library';
        }
        
        //public_path
        if(empty($this->resource['public_path'])) { $warnings['public_path'] = 'Public path missing'; }
        elseif(!is_dir($this->resource['public_path'])) {
            $warnings['public_path'] = 'folder no found in library';
        }*/
                
        function relativePath($path) { global $document_root; return str_replace($document_root,'',$path); }
        	
        //$this->resource['ROOT'] = relativePath($this->resource['public_path']);
        //$this->resource['LIBRARY_ROOT'] = relativePath($this->resource['library_path']);
        $this->resource['ROOT'] = '';
        $this->resource['LIBRARY_ROOT'] = '';
        $this->resource['APPLICATION_ROOT'] = relativePath($this->resource['app_path'].'/'.$this->resource['app_name']);
                
       
    
        if(!empty($warnings)) { 
            //send $warnings to object response var
            $this->response['warnings'] = $warnings; 
            $this->response['submit_fail'] = true;
            if(isset($this->resource['add_configs'])) $this->response['showconfigs'] = true;
            return;
        }
        else {
            //create app...
            $this->response['submit_pass'] = false;
            
            //define important path
            $app_base = $this->resource['app_path'];
            $app_folder = $this->resource['app_name'];          
            $app_path = $app_base.'/'.$app_folder;
                    
                        
            //include core
            include($pk_path.'/Core.php'); 
            include($pk_path.'/Config.php'); 
            include($pk_path.'/Registry.php'); 
            
            //set path core
            Peak_Core::init();
            Peak_Core::initApp($app_path, '');   
            
            $config = Peak_Registry::o()->core_config;
            
            
            $paths = $config->getVars();
            unset($paths['library_path'], $paths['libs_path'], $paths['theme_path'], $paths['views_themes_path']);
            
            //echo '<pre>';
            //print_r($paths);
            
               
            //create application paths           
            foreach($paths as $path) {
            	if(!@mkdir($path)) {
            		_die('Fail to create folder '.$path);
            	}
            }
            
            
            include($pk_path.'/Core/Extension/Codegen.php');

            $cg = new Peak_Core_Extension_Codegen();
            
            //create empty configs.php
            if(!(@file_put_contents($app_path.'/configs.php', ' '))) _die('Fail to create configs.php file');
            
            $configs =  array('PROJECT_NAME'     => $this->resource['app_name'],
    	                      'PROJECT_DESCR'    => '',
    	                      'DEV_MODE'         => 'true',
    	                      'APP_DEFAULT_CTRL' => 'index',
    	                      'SVR_URL'          => 'http://127.0.0.1',
    	                      'ROOT'             => $this->resource['ROOT'],
    	                      'LIBRARY_ROOT'     => $this->resource['LIBRARY_ROOT'],
    	                      'APPLICATION_ROOT' => $this->resource['APPLICATION_ROOT'],
    	                      'ZEND_LIB_ROOT'    => '',
    	                      'ENABLE_PEAK_CONTROLLERS' => 'false');
            
            $configs_result = $cg->saveSample('Configs', $app_path.'/configs.php', $configs, true);
     
            //create empty bootstrap.php
            if(!(@file_put_contents($app_path.'/bootstrap.php', ' '))) _die('Fail to create bootstrap.php file');
            $bt_result = $cg->saveSample('Bootstrap', $app_path.'/bootstrap.php', array(), true);             
            
            
            $this->response['submit_pass'] = true;
            
        }                
        
    }
}

/**
 * MISC VARS
 */

//base64 coded images
$img = array();
$img['peaklogo'] = <<< EOFILE
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
EOFILE;

/**
 * FUNCTIONS
 */

function _die($msg)
{
	die('<div style="padding:15px 20px;margin:40px;background:#000;color:#fff;border:1px solid #ccc;">'.$msg.'</div>');
}

//echo/return form $_POST var stripped of html or not if exists
function pval($post_key, $default = false, $strip = false, $echo = true)
{
    if(isset($_POST[$post_key])) $val = ($strip) ? strip_tags($_POST[$post_key]) : $_POST[$post_key];
    else $val = ($default) ? $default : '';
    
    if($echo) echo $val;
    else return $val;
}

//echo class="fail" if warnings is found in $responde
function warning($key)
{
	global $response;
	if(isset($response['warnings'][$key])) echo 'class="fail"';
}

/**
 * OBJECT PROCESS
 */

//start dispatching action
$d = new appcreate();
$d->setRecursivity(true,3);
$d->start();

//get dispatching result(s)
$response = $d->response;

/**
 * HTML PAGE
 */
?>

<html>
 <head>
  <!--<link rel="stylesheet" href="../themes/appcreate/default.css" />-->
  <style type="text/css">
  <!--
  body{background:#F5F5EA url(appcreate.php?img=peaklogo) 50% 10px no-repeat;font:14px "Tahoma";margin:0 auto;}a,a:link,a:visited{color:#4C92E9;}a:hover{color:#13519B;}strong{font-weight:bold;}hr{border:0;border-top:1px solid #ccc;height:1px;margin:20px 0;padding:5px 0 10px 0;}input,select,textarea{font:12px "Verdana";padding:4px 4px;border:1px solid #ccc;}form input,form select,form textarea{width:450px;margin-bottom:10px;background:#F9F9F9;}form label{float:left;width:120px;font-weight:bold;}form input.right{float:right;}form .errors{color:red;font-size:11px;line-height:20px;margin:0 0 20px 0;}form.large {}form input.fail{border:1px solid red;}form small{color:#777;}#wrap{margin:140px auto;width:650px;}.lbox{float:left;margin-right:20px;}.button{background:#EEEEEE;width:100px;}.clear{clear:both;height:1px;}.chkbox{width:1px;}.box{text-align:left;line-height:20px;background:#fff;padding:0 20px 10px 20px;margin:10px 10px 10px 0;border:1px solid #BEBCA5;box-shadow: -1px -1px 15px #ccc;-webkit-box-shadow: -1px 1px 15px #ccc;-moz-box-shadow: -1px 1px 15px #BEBEBE;line-height:25px;}.box div.title{text-align:left;font-size:18px;border-bottom:1px solid #BEBCA5;border-top:0px solid #BEBCA5;margin:0px -20px 20px -20px;background:#F1F1D9;padding:10px 10px;text-shadow:#ccc 1px 1px 1px;}.box div.subtitle{background:#f5f5f5;margin:-20px -20px 10px -20px;padding:5px 15px;border-bottom:1px solid #ccc;}.box div.footer{margin:35px -20px -10px -20px;padding:6px 10px;background:#f1f1f1;border-top:1px solid #ccc;font-size:11px;}
  -->
  </style>
 </head>
 <body>
 
 <div id="wrap">
 
 <?php if((isset($response['submit_pass'])) && (($response['submit_pass']))): ?>
  <h3 style="text-align:center;">Application <i><?php pval('app_name'); ?></i> created successfully!</h3> 
  <?php exit(); ?>
 <?php endif; ?>
 
 <form method="post" action="">
   
  <div class="box">
	 <div class="title">Create a new application</div>
	 <div class="subtitle">This tools will help you to create complete application directory structure.</div>
	 
	 &nbsp;<br />
	 
	 <?php if(isset($response['warnings'])) : ?>
      Form contains errors:<br />
      <div class="errors">
      <?php foreach($response['warnings'] as $k => $v) echo $k.' : '.$v.'<br />'; ?>
      </div>
     <?php endif; ?>
	 
	 
	 <label>&nbsp;</label><small>aA-aZ, 0-9, no spaces. This will be your folder under your <i>App path</i></small><br />
	 <label>App name:</label>
	 <input name="app_name" type="text" value="<?php pval('app_name'); ?>" <?php warning('app_name'); ?> /><br />	 

	 <label>&nbsp;</label><small>Path where your <i>App name</i> folder will be created.</small><br />
	 <label>App path:</label>
     <input name="app_path" type="text" value="<?php pval('app_path',$abspath.'application'); ?>" <?php warning('app_path'); ?> /><br />

     <!--    
     <label>&nbsp;</label><small>Path where is /Peak/ folder.</small><br />
     <label>Library path:</label>
     <input name="library_path" type="text" value="<?php pval('library_path', $abspath.'library'); ?>" <?php warning('library_path'); ?> /><br />
     
     <label>&nbsp;</label><small>Public path of your application</small><br />
     <label>Public path:</label>
     <input name="public_path" type="text" value="<?php pval('public_path',$abspath.'public_html'); ?>" <?php warning('public_path'); ?> />  
     -->
     
    <!-- <hr />
     <input class="chkbox" name="create_app_path" type="checkbox" /> Create <i>App path</i> folder if don't exists<br />
     <input class="chkbox" name="create_public_path" type="checkbox" /> Create <i>Public path</i> folder if don't exists<br />
     
     <hr />-->
     
     <div style="float:right; margin:20px 30px 0 0;"><input class="button" name="submit" type="submit" value="create!" /></div>
     <div class="clear"></div>
     
     
     <div class="footer"><strong>!important:</strong> This file SHOULD BE DELETED and NOT USED in PRODUCTION environment.</div>
     
   </div><!-- /box -->
  </form>
  
  </div><!-- /wrap -->
   
 </body>
</html>