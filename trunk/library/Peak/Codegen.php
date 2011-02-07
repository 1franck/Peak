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
	 * PHP open tag
	 */
	const PHP_OPEN_TAG = '<?php';
	
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
	 * @param  string $filepath filepath       where content will be saved, if file doesn't exists it will create it.
	 * @param  bool   $add_php_open_tag        add php open tag in file content
	 * @param  misc   $file_put_contents_flags add file_put_contents_flags() flags
	 * @return bool
	 */
	public function save($filepath, $add_php_open_tag = false, $file_put_contents_flags = 0)
	{
		$data = $this->preview();
		
		if($add_php_open_tag) {
		    $data = Peak_Codegen::PHP_OPEN_TAG . Peak_Codegen::LINE_BREAK . $data;
		}
		
		$result = @file_put_contents($filepath, $data, $file_put_contents_flags);
		return $result;
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