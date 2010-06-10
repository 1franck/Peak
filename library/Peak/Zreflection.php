<?php

/**
 * Zend reflection class wrapper
 * 
 * @uses     Zend_Reflection classes !important
 * @author   Francois Lajoie
 * @version  $Id$
 */
class Peak_Zreflection
{
    
    
    public $class;
    
    /**
     * Load Zend_Reflection_Class
     *
     * @param string $class
     * @param bool   $autoload
     */
    public function loadClass($class,$autoload = true)
    {
        if(class_exists($class,$autoload))
        {
            $this->class = new Zend_Reflection_Class($class);

            /*$methods = $this->class->getMethods();
            $properties = $this->class->getProperties();
            $constants = $this->class->getConstants();
            
            $class_properties = getClassDeclaration();  */
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
     * @return array
     */
    public function getClassDocTags()
    {
        //get class doc comment tags
        try {
            $classDoc = new Zend_Reflection_Docblock($this->class->getDocComment());
            $classDoctags = (is_object($classDoc)) ? $classDoc->getTags() : '';
        }
        catch(Exception $e) { $classDoctags = ''; }
        return $classDoctags;
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
        $properties[] = ($this->class->isInterface()) ? 'interface' : ' class';

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
     * Get params object as list
     *
     * @uses  Zend_Reflection_Parameter
     * @param object $params
     */
    function paramsAsList($paramsObject,$router)
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
    function paramsToArray($method)
    {
        global $doc;
        $params = $doc->getMethod($method)->getParameters();
        $paramsArray = array();
        foreach($params as $param) {
            $paramsArray[] = $param->name;
        }
        return $paramsArray;
    }

    
    
}