<?php
/**
 * Retreive directory sizes and number of files
 * 
 * @uses    RecursiveDirectoryIterator
 * @author  Francois Lajoie
 * @version $Id$
 */
class Peak_Spl_Dirinfo
{
	/**
	 * Size
	 * @var integer
	 */
	protected $_size = 0;

	/**
	 * Number of files
	 * @var integer
	 */
	protected $_nbfiles = 0;

	/**
	 * Gather information about directory
	 * 
	 * @param string $path
	 */
	public function __construct($path) 
	{
		$it = new RecursiveDirectoryIterator($path);

		foreach (new RecursiveIteratorIterator($it) as $f => $c) {
			$size = $c->getSize();
			$this->_size += $size;
			++$this->_nbfiles;
		}
	}

	/**
	 * Return directory size
	 *
	 * @param  bool $format
	 * @return string|integer
	 */
	public function getSize($format = false)
	{
		if(!$format) return $this->_size;
		else {
            $unit = array('B','kB','MB','GB','TB','PB');
            return @round($this->_size/pow(1024,($i=floor(log($this->_size,1024)))),2).' '.$unit[$i];
		}
	}

	/**
	 * Return number of files of directory 
	 *
	 * @return integer
	 */
	public function getNbfiles()
	{
		return $this->_nbfiles;
	}

}