<?php
/**
 * Simple codegen Example
 * @version $Id$
 */
class SimpleClass extends Peak_Codegen
{
    public $sentence = 'hi!';
	
	public function preGenerate()
	{
		$this->sentence = 'hello!';
	}
	
    public function generate()
    {
        return self::PHP_OPEN_TAG.self::LINE_BREAK.'echo "'.$this->sentence.'";';
    }
	
}