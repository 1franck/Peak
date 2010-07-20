<?php
$file_to_test = realpath('./../library/Peak/View.php');
include($file_to_test);
echo 'Tested file: '.$file_to_test.'<br />';

$file_to_test = realpath('./../library/Peak/View/Theme.php');
include($file_to_test);
echo 'Tested file: '.$file_to_test.'<br />';

include('./../library/Peak/Exception.php');

class TestOfView extends UnitTestCase
{
    
    public function peak($parent = false)
    {
    	if(!$parent) return realpath('./../library/Peak');
    	else return realpath('./../library');
    }
    
    function testOfInstanciate()
    {  	
    	 $this->view = new Peak_View();
    	 $this->assertTrue(is_a($this->view,'Peak_View') ,'$view is not an object of Peak_View');
    }
    
    function testOfProperties()
    {    	
    	$vars = $this->view->getVars();
    	$engine = $this->view->engine();
    	$this->assertTrue(is_array($vars),'$vars is not a valid array');
    	$this->assertTrue(empty($vars),'$vars is not empty');
    	$this->assertTrue(is_string($engine),'engine should be not set');
    }
    
    function testOfViewVars()
    {    	
    	$this->view->test = 'abc';
    	$this->assertTrue(($this->view->test === 'abc'),'setting var $test failed');
    	
    	unset($this->view->test);
    	$this->assertTrue(!isset($this->view->test),'$test variable should be unset');
    	$this->assertFalse(isset($this->view->test456),'$test456 variable should be not set');

    }
    
    function testOfViewMethod()
    {  	
    	define('DEV_MODE',true);    	
    	//$this->expectError($this->view->unknowmethod(),'unknow method should trigger error with DEV_MODE = true');   	
    	//$this->expectException($this->view->unknowmethod(),'unknow method should trigger error with DEV_MODE = true');   	
    		
    	$c = $this->view->countVars();
    	$this->assertTrue(is_integer($c),'countVars() should return integer');
    	$this->assertTrue(($c == 0),'countVars() should return 0');

    	$this->view->test = 'abc';
    	$c = $this->view->countVars();
    	$this->assertTrue(($c == 1),'countVars() should return 1');
    	
    	$this->view->resetVars();
    	$c = $this->view->countVars();
    	$this->assertTrue(($c == 0),'countVars() should return 0 after calling resetVars()');
    	
    	$vars = $this->view->getVars();

    }
    
    function testOfViewTheme()
    {
    	$theme = $this->view->theme();
    	$this->assertTrue(is_a($theme,'Peak_View_Theme') ,'$theme is not an object of Peak_View_Theme');
    	
    	$options = $theme->getOptions();
    	$this->assertTrue(is_array($options) ,'$options should be an array');
    	$this->assertTrue(empty($options) ,'$options should be empty');
    }
    
    function testOfViewEngine()
    {
    	try {   		
    		$this->view->render('test','test');
    	}
    	catch (Peak_Exception $e) {
    		$exception = 'render() expected to fail when no engine have been loaded before';
    	}
    	
    	$this->assertTrue(isset($exception), $exception);
    	
    	include($this->peak().'/View/Render.php');
    	include($this->peak().'/View/Render/Partials.php');
    	include($this->peak().'/View/Render/Layouts.php');
    	include($this->peak().'/View/Render/Json.php');
    	
    	$this->view->setRenderEngine('unknow');
    	$engine = $this->view->engine();
    	$this->assertTrue(is_a($engine,'Peak_View_Render_Partials'),'Peak_View_Render_Partials should be set when calling setRenderEngine() with unknown engine.');    	
    	
    	$this->view->setRenderEngine('Layouts');
    	$engine = $this->view->engine();
    	$this->assertTrue(is_a($engine,'Peak_View_Render_Layouts'),'Peak_View_Render_Layouts should be set when calling setRenderEngine(\'Layouts\').');
    	
    	$this->view->setRenderEngine('Json');
    	$engine = $this->view->engine();
    	$this->assertTrue(is_a($engine,'Peak_View_Render_Json'),'Peak_View_Render_Json should be set when calling setRenderEngine(\'Json\').');
    }
    
    
    function testOfHelper()
    {
    	define('LIBRARY_ABSPATH',$this->peak(true));
    	include($this->peak().'/View/Helper.php');
    	include($this->peak().'/View/Helpers.php');
    	$object = $this->view->helper()->icons;
    	$this->assertTrue(is_a($object,'Peak_View_Helper_Icons'),'Peak_View_Helper_Icons should be set when calling helper()->icons');
    	
    	try {   		
    		$test = @$this->view->helper()->ic767ons;
    		$this->assertFalse(is_object($test),'helper() excepted to fail when calling unknow helper name');
    	}
    	catch (Peak_Exception $e) {
    		$exception = 'helper() excepted to fail when calling unknow helper name';
    	}    	
    	$this->assertTrue(isset($exception), $exception);
    	
    	try {   		
    		$test = @$this->view->unknowhelper();
    		$this->assertFalse(is_object($test),'unknowhelper() excepted to fail when calling unknow doesn\'t exists');
    	}
    	catch (Peak_Exception $e) {
    		$exception = 'unknowhelper() excepted to fail when calling unknow doesn\'t exists';
    	}    	
    	$this->assertTrue(isset($exception), $exception);

    	$object = $this->view->text();
    	$this->assertTrue(is_a($object,'Peak_View_Helper_Text'),'Peak_View_Helper_Text should be set when calling text()');
   	
    }
    
    
    function testOfViewIni()
    {
    	//$path = realpath('./temps/view.ini');
    	define('VIEWS_INI_ABSPATH', realpath('./temps/'));
    	//include($path);
    	
    	$this->view->resetVars();
    	$c = $this->view->countVars();
    	$this->assertTrue(($c == 0),'countVars() should return 0 after calling resetVars()');
    	
    	$this->view->iniVar('view.ini');
    	$c = $this->view->countVars();
    	$this->assertTrue(($c == 5),'countVars() should return 5 after calling iniVar()');
    }
    
   
}