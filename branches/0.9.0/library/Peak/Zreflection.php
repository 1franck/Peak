<?php
/**
 * Zend reflection class wrapper
 * 
 * @descr    This will help you to resolve some complex treatments needed to gather informations about php classes from Zend_Reflection components.
 * 
 * @uses     Zend_Reflection classes + Zend_Loader !important
 * @author   Francois Lajoie
 * @version  $Id$
 */
class Peak_Zreflection
{

    /**
     * Zend_Reflection_Class object
     * @var object
     */
    public $class;

    /**
     * Load Zend_Reflection_Class
     *
     * @uses  Zend_Reflection_Class
     * 
     * @param string $class
     * @param bool   $autoload
     */
    public function loadClass($class, $autoload = true)
    {
        if(class_exists($class, $autoload)) {
            $this->class = new Zend_Reflection_Class($class);
        }
    }

    /**
     * Retreive class description 
     * 
     * @uses   Zend_Reflection_Docblock
     * @param  string $type (short or long)
     * @return string
     */
    public function getClassDoc($type = 'short')
    {
        //get short or long descr from Zend_Reflection_Docblock
        try {
            $classdoc = new Zend_Reflection_Docblock($this->class->getDocComment());
            $descr = ($type === 'short') ? $classdoc->getShortDescription() : $classdoc->getLongDescription();
        }
        catch(Exception $e) { $descr = ''; }
        return $descr;
    }

    /**
     * Retreive class description tags
     *
     * @uses   Zend_Reflection_Docblock
     * 
     * @return array
     */
    public function getClassDocTags()
    {
        //get class doc comment tags
        $result = array();
        try {
            $classDoc = new Zend_Reflection_Docblock($this->class->getDocComment());
            $tags = (is_object($classDoc)) ? $classDoc->getTags() : array();

            foreach($tags as $tag) {
                $result[] = array('name' => trim($tag->getName()),
                                  'description' => trim($tag->getDescription()));
            }

        }
        catch(Exception $e) { $result = array(); }
        return $result;
    }

    /**
     * Get class properties, parent class and interfaces
     *
     * @return array
     */
    public function getClassDeclaration()
    {
        $declaration = array();

        //class delcaration
        $properties = array();
        if($this->class->isAbstract())    $properties[] = 'abstract';
        if($this->class->isFinal())       $properties[] = 'final';
        if($this->class->isInternal())    $properties[] = 'internal';
        if($this->class->isUserDefined()) $properties[] = 'user-defined';
        $properties[] = ($this->class->isInterface()) ? 'interface' : 'class';

        $declaration['properties'] = $properties;

        //parent class
        if($this->class->getParentClass()) $declaration['parent'] = $this->class->getParentClass()->name;

        //class interface
        $interfaces = array();
        if($this->class->getInterfaces()) {
            foreach($this->class->getInterfaces() as $k => $interface) $interfaces[] = $interface->name;
        }

        $declaration['interfaces'] = $interfaces;

        return $declaration;
    }
    
    /**
     * Get class constants
     *
     * @return array
     */
    public function getConstants()
    {
        return $this->class->getConstants();
    }
        
    /**
     * Get class methods, self and parents
     *
     * @return array
     */ 
    public function getMethods()
    {
        return $this->class->getMethods();
    }
    
    /**
     * Get class parent methods only
     *
     * @return array
     */
    public function getParentMethods()
    {
        $result = $this->getMethodsByInheritance();
        return $result['parent'];
    }
    
    /**
     * Get class self methods only
     *
     * @return array
     */
    public function getSelfMethods()
    {
        $result = $this->getMethodsByInheritance();
        return $result['self'];
    }

    /**
     * Get methods separated by inheritance(self or parent)
     *
     * @return array
     */
    public function getMethodsByInheritance()
    {
    	$result = array('self' => array(), 'parent' => array());
    	
    	$methods = $this->class->getMethods();
        
    	if($methods) {
            $classname = strtolower($this->class->getName());
    		foreach ($mets as $m) {
    			if(strtolower($this->getMethodClassname($m->name)) !== $classname) $result['parent'][] = $m;
    			else $result['self'][] = $m;
    		}
    	}
    	
    	return $result;
    }

    /**
     * Get class declaring class name of a method 
     *
     * @param  string $name
     * @return string
     */
    public function getMethodClassname($name)
    {
        return $this->class->getMethod($name)->getDeclaringClass()->getName();
    }

    /**
     * Get method visibility
     *
     * @param  string $name
     * @return string
     */
    public function getMethodVisibility($name)
    {
        $v = array();

        if($this->class->getMethod($name)->isPublic()) $v['visibility'] = 'public';
        elseif($this->class->getMethod($name)->isPrivate()) $v['visibility'] = 'private';
        elseif($this->class->getMethod($name)->isProtected()) $v['visibility'] = 'protected';

        if($this->class->getMethod($name)->isStatic()) $v['static'] = 'static';

        return implode(' ',$v);
    }

    /**
     * Get a method description from current class
     *
     * @uses   Zend_Reflection_Method
     * 
     * @param  string $method
     * @param  string $type (short or long)    
     * @return string
     */
    public function getMethodDoc($method, $type = 'short')
    {
    	//Method description
    	try {
    		$oMethod = new Zend_Reflection_Method($this->class->getName() ,$method);
    		$shortdescr = $oMethod->getDocblock()->getShortDescription();
    		$longdescr = $oMethod->getDocblock()->getShortDescription();

    		if($shortdescr === $longdescr) $longdescr = null;
    		elseif((empty($shortdescr)) && (!empty($longdescr))) {
    			$shortdescr = $longdescr;
    			$longdescr = null;
    		}

    	}
    	catch(Exception $e) { $shortdescr = null; $longdescr = null; }
    	
    	if($type === 'short') return $shortdescr;
    	else return $longdescr;
    }

    /**
     * Get method comment tags
     *
     * @param  string $method
     * @return object
     */
    public function getMethodDocTags($method)
    {
    	try {
			$oDocBlock = new Zend_Reflection_Docblock($this->class->getMethod($method)->getDocblock()->getContents());
			$comment_tags = $oDocBlock->getTags();
		}
		catch(Exception $e) { $comment_tags = ''; }
		
		return $comment_tags;
    }
    
    /**
     * Get class properties
     *
     * @return array
     */ 
    public function getProperties()
    {
        return $this->class->getProperties();
    }
    
    /**
     * Get class parent properties only
     *
     * @return array
     */
    public function getParentProperties()
    {
        $result = $this->getPropertiesByInheritance();
        return $result['parent'];
    }
    
    /**
     * Get class self properties only
     *
     * @return array
     */
    public function getSelfProperties()
    {
        $result = $this->getPropertiesByInheritance();
        return $result['self'];
    }

    /**
     * Get properties separated by inheritance
     *
     * @return array
     */
    public function getPropertiesByInheritance()
    {
    	$result = array('self' => array(), 'parent' => array());
    	
    	$props = $this->class->getProperties();
    	if($props) {
            $classname = strtolower($this->class->getName());
    		foreach ($props as $prop) {
    			if(strtolower($this->getPropertyClassname($prop->name)) !== $classname) $result['parent'][] = $prop;
    			else $result['self'][] = $prop;
    		}
    	}
    	
    	return $result;
    }

    /**
     * Get class declaring class name of a property 
     *
     * @param  string $name
     * @return string
     */
    public function getPropertyClassname($name)
    {
        return $this->class->getProperty($name)->getDeclaringClass()->getName();
    }

    /**
     * Get property visibility
     *
     * @param  string $name
     * @return string
     */
    public function getPropertyVisibility($name)
    {
        $v = array();

        if($this->class->getProperty($name)->isPublic()) $v['visibility'] = 'public';
        elseif($this->class->getProperty($name)->isPrivate()) $v['visibility'] = 'private';
        elseif($this->class->getProperty($name)->isProtected()) $v['visibility'] = 'protected';

        if($this->class->getProperty($name)->isStatic()) $v['static'] = 'static';

        return implode(' ',$v);
    }

    /**
     * Get property description from the current class
     *
     * @use    ReflectionProperty, Zend_Reflection_Docblock
     * 
     * @param  string $property
     * @param  string $type
     * @return string
     */
    public function getPropertyDoc($property, $type = 'short')
    {
    	try {

    		$oProperty = new ReflectionProperty($this->class->getName(), $property);
    		$oDocblock = new Zend_Reflection_Docblock($oProperty->getDocComment());
    		$shortdescr = $oDocblock->getShortDescription();
    		$longdescr = $oDocblock->getLongDescription();

    		if($shortdescr === $longdescr) $longdescr = null;
    		elseif((empty($shortdescr)) && (!empty($longdescr))) {
    			$shortdescr = $longdescr;
    			$longdescr = '';
    		}

    	}
    	catch(Exception $e) { $shortdescr = ''; $longdescr = ''; }
    	
    	if($type === 'short') return $shortdescr;
    	else return $longdescr;
    }

    /**
     * Get property comment tags
     *
     * @param  string $property
     * @return array
     */
    public function getPropertyDocTags($property)
    {
    	try {
    		$oProperty = new ReflectionProperty($this->class->getName(), $property);
    		$oDocblock = new Zend_Reflection_Docblock($oProperty->getDocComment());
			$comment_tags = $oDocblock->getTags();
		}
		catch(Exception $e) { $comment_tags = ''; }
		
		return $comment_tags;
    }

    /**
     * Get params object as list
     *
     * @uses   Zend_Reflection_Parameter
     * 
     * @param  object $params
     * @param  array  $router 
     * @return string
     */
    public function paramsAsList($paramsObject, $router)
    {
        $params_list = array();

        foreach($paramsObject as $param) {

            //tr to retreive param type
            try {
                $p = new Zend_Reflection_Parameter($router,$param->name);
                $param->type = $p->getType();
                $param->type = (empty($param->type)) ? '' : $param->type.' ';
                if($p->isPassedByReference()) $param->type = $param->type.'&';
            }
            catch(Exception $e) { $param->type = ''; }

            $params_list[] = ($param->isOptional()) ? '['.$param->type.' <strong><i>$'.$param->name.'</i></strong>]' : $param->type.' <strong><i>$'.$param->name.'</i></strong>';
        }
        return implode(', ',$params_list);
    }

    /**
     * Get params object as simple array
     *
     * @param  string $method
     * @return array
     */
    public function paramsToArray($method)
    {
        $params = $this->class->getMethod($method)->getParameters();
        $paramsArray = array();
        foreach($params as $param) {
            $paramsArray[] = $param->name;
        }
        return $paramsArray;
    }

    /**
     * Get tags object as array
     *
     * @param  object $tag
     * @return array
     */
    public function docTagsToArray($tag)
    {
    	$result = array('name' => '','type' => '', 'variableName' => '', 'description' => '');
    	
    	if(method_exists($tag,'getName')) $result['name'] = $tag->getName();
    	if(method_exists($tag,'getType')) $result['type'] = $tag->getType();
    	if(method_exists($tag,'getVariableName')) $result['variableName'] = $tag->getVariableName();
    	if(method_exists($tag,'getDescription')) $result['description'] = $tag->getDescription();
    	
    	return $result;
    }
}