<?php
/**
 * Peak welcome controller. This is the default controller for Peak/Application/genericapp.ini
 * Its juste a welcome (hello words) page... :P
 * 
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_Controller_Internal_Pkwelcome extends Peak_Controller_Action
{

	/**
	 * Setup controller
	 */
    public function preAction()
    {
        $this->view->engine('VirtualLayouts');
        $this->view->cache()->disable();

		$this->layout();
		
		error_reporting(false);
    }
    
	/**
	 * Intro action
	 */
    public function _index()
    {
        $this->layout();
    }
	
		
	/**
	 * Controller View Layout
	 */
	private function layout()
	{
		$twitter_bs = file_get_contents(LIBRARY_ABSPATH.'/Peak/Vendors/TwitterBootstrap/2.1.0/css/bootstrap.min.css');
		$layout = '<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Welcome to Peak Framework App Welcome Page. Damn! that\'s a long title</title>
    <meta name="description" content="">
    <meta name="author" content="Francky the bad guy">

    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->

    <!-- Le styles -->

	<link href="http://fonts.googleapis.com/css?family=Ubuntu" rel="stylesheet" type="text/css" />
    <style type="text/css">
	  /* TWITTER CSS */
	  '.$twitter_bs.'
	  /* --------------------- */
      body {
        /*padding-top: 60px;*/
		font-family: "Ubuntu", sans-serif;
      }
      .navbar {
        position:inherit;
        margin-bottom:30px;
      }
	  .hero-unit {
		padding:27px 40px;
	  }
	  .hero-unit h1 {
		font-size:42px;
	  }
	  textarea {
	 	  width:100%;
		  height:400px;
		  font-family: "Consolas", "Lucida Console", sans-serif;
	  }
    </style>
  </head>

  <body>

    <div class="navbar navbar-inverse navbar-fixed-top">
      <div class="navbar-inner">
        <div class="container-fluid">
          <a class="brand" href="#">Your App</a>
        </div>
      </div>
    </div>

    <div class="container">
			<div class="hero-unit">
        		
				<h1>Welcome, young padawan!</h1>
				<h4>If you see this, it\'s because you have successfully launched your Peak Framework Application but you don\'t have yet a configuration file.</h4>
				<h4>As we are nice, we provided you a generic configuration to start with:</h4>
				
				<textarea spellcheck="false">'.(file_get_contents(LIBRARY_ABSPATH.'/Peak/Application/genericapp.ini')).'</textarea>
        
				<div class="clear"></div>
			</div>
	</div>

  </body>
</html>';
		
		$this->view->setLayout($layout);
	}

}