<?php
/**
 * Generate Front controller class
 * 
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_Codegen_Front extends Peak_Codegen
{
    
	/**
	 * Peak_Codegen_Class instance
	 * @var object
	 */
    public $class;
	
    /**
	 * Create front controller base with Peak_Codegen_Class 
	 */
    public function __construct()
    {
    	$this->class = new Peak_Codegen_Class();
    	
    	$this->class->setName('Front')
		            ->setExtends('Peak_Controller_Front')
		            ->docblock()->setTitle('App Front Controller');
		           
		$this->class->method('preDispatch')->docblock()->setTitle('Do something before a controller is dispatched');   	
		
		$this->class->method('postDispatch')->docblock()->setTitle('Do something after a controller is dispatched');   	
		
		$this->class->method('postRender')->docblock()->setTitle('Do something after a controller view is rendered');   	
		
    }
    
    /**
     * Set class name
     *
     * @param  string $name
     * @return object
     */
    public function setName($name)
    {
    	$this->class->setName($name)
    	            ->docblock()->setTitle($name);
    	return $this;
    }
    
    /**
     * Generate class
     */
	public function generate()
	{
		return $this->class->generate();
	}
	
}