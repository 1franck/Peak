<?php
/**
 * Abstract class for code generation
 * 
 * @author  Francois Lajoie
 * @version $Id$
 */
abstract class Peak_Codegen
{
	/**
	 * Indentation space
	 */
	const INDENTATION_SPACE = 4;
	
	/**
	 * Line break symbol
	 */
	const LINE_BREAK = "\n";
	
	
	/**
	 * Get preview of generated code
	 *
	 * @return string
	 */
	public function preview()
	{
		$this->preGenerate();
		return $this->generate();
	}
	
	/**
	 * Save generated code
	 *
	 * @param string $filepath
	 */
	public function save($filepath)
	{
		$this->preGenerate();
		$data = $this->generate();
		file_put_contents($filepath, $data);
	}
	
	/**
	 * Need to be overloaded by child but optionnal
	 */
	public function preGenerate() { }
	
	/**
	 * Need to be overloaded by child
	 */
	public function generate() { }
	
}