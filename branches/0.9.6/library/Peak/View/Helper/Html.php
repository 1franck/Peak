<?php
/**
 * @deprecated
 * View basic html functions
 * 
 * @author  Francois Lajoie
 * @version $Id: html.php 545 2012-11-07 04:21:27Z snake386@hotmail.com $
 */
class Peak_View_Helper_Html
{

	/**
     * Generate html doctype
     *
     * @param  string $type
     * @return string
     */
	public function doctype($type = 'HTML5')
	{
		$dt = array('XHTML11'             => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">',
		'XHTML1_STRICT'       => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
		'XHTML1_TRANSITIONAL' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">',
		'XHTML1_FRAMESET'     => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">',
		'XHTML_BASIC1'        => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.0//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd">',
		'XHTML_MP1'           => '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">',
		'XHTML_MP11'          => '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.1//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile11.dtd">',
		'XHTML_MP12'          => '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">',
		'HTML4_STRICT'        => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">',
		'HTML4_LOOSE'         => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">',
		'HTML4_FRAMESET'      => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">',
		'HTML5'               => '<!DOCTYPE html>');

		if(!isset($dt[$type])) $type = 'XHTML1_STRICT';
		return $dt[$type]."\n";
	}

	/**
     * Return meta name/content tag
     *
     * @param  string $name
     * @param  string $content
     * @return string
     */
	public function meta($name, $content)
	{
		return '<meta name="'.$name.'" content="'.$content.'" />';
	}

	/**
     * Transform css file or array of css files into meta link stylesheet tags
     *
     * @param  string/array $files
     * @return string
     */
	public function meta_link_ss($files)
	{
		$html = '';
		if(!is_array($files)) $html = '<link rel="stylesheet" href="'.$files.'" type="text/css" />';
		else {
			foreach($files as $file) $html .= '<link rel="stylesheet" href="'.$file.'" type="text/css" />';
		}
		return $html;
	}

	/**
     * Transform js file or array of js files into meta script tags
     *
     * @param string/array $files
     * @return string
     */
	public function meta_script($files)
	{
		$html = '';
		if(!is_array($files)) $html = '<script type="text/javascript" src="'.$files.'"></script>';
		else {
			foreach($files as $file) $html .= '<script type="text/javascript" src="'.$file.'"></script>';
		}
		return $html;
	}

	/**
     * Put css code inside <style> tags
     *
     * @param  string $code
     * @return string
     */
	public function style_code($code)
	{
		return '<style type="text/css"><!-- '.$code.' --></style>';
	}

	/**
     * Put js code inside <script>
     *
     * @param  string $code
     * @param  string $type
     * @return string
     */
	public function script_code($code, $type = 'text/javascript')
	{
		return '<script type="'.$type.'">'.$code.'</script>';
	}

	/**
     * Put jquery code inside <script>
     *
     * @param  string $code
     * @return string
     */
	public function jquery_code($code)
	{
		return '<script type="text/javascript">$(document).ready(function() { '.$code.' });</script>';
	}

	/**
     * Transform to img html 
     *
     * @param  string $img    image url file path name
     * @param  string $attrs  html tag attribute
     * @return string
     */
	public function img($img, $attrs = array('alt' => ''))
	{
		$img = trim(stripslashes(strip_tags($img)));

		$attrs_html = '';

		if(is_array($attrs)) {
			foreach($attrs as $attr => $val) {
				$attrs_html .= ' '.$attr.'="'.$val.'"';
			}
		}

		return '<img src="'.$img.'" '.$attrs_html.' />';
	}


	/**
     * Transform array of link to html string
     *
     * @param array $array
     * @param string $class
     * @param string $sep
     * @return string
     */
	public function links($array, $class = '', $sep = ' | ')
	{
		$result = '';
		if(is_array($array)) {
			$nb_of_links = count($array); $i = 0;
			foreach($array as $title => $link) {
				++$i;
				$class = (!isset($link)) ? 'active' : '';
				$result .= '<a href="'.$link.'" class="'.$class.'">'.$title.'</a>';
				if($i < $nb_of_links) $result .= $sep;
			}
		}
		return $result;
	}

	/**
     * Simple 2 strings shifting function
     *
     * @param string $a
     * @param string $b
     * @return string
     */
	public $html_cycle  = 1;  //see cycle() method
	public function cycle($a,$b)
	{
		global $html_cycle;
		$html_cycle = ($html_cycle === 1) ? 2 : 1;
		return ($html_cycle == 1) ? $a : $b;
	}


	/**
     * Transform data record array to html table
     *
     * @param array $data
     * @param array $options
     * @return string
     */
	public function table($data,$options = array())
	{
		$html = '';

		$default = array('attrs' => '',
		'thead' => null,
		'tfoot' => null,
		'row_cycle' => null);

		$options = array_merge($default,$options);

		$html .= '<table '.$options['attrs'].'>';

		//parse thead
		if(is_array($options['thead'])) {
			$html .= '<thead><tr>';
			foreach($options['thead'] as $head) $html .= '<td>'.$head.'</td>';
			$html .= '</tr></thead>';
		}

		//parse data
		if(is_array($data)) {
			$html .= '<tbody>';
			foreach($data as $id => $data) {

				$html .= (is_array($options['row_cycle'])) ? '<tr class="'.html_cycle($options['row_cycle'][0],$options['row_cycle'][1]).'">' : '<tr>';
				if(is_array($data)) {
					foreach($data as $k => $v) {
						$html .= '<td>'.$v.'</td>';
					}
					$html .= '</tr>';
				}
				else $html .= '<td>'.$id.'</td><td>'.$data.'</td></tr>';
			}
			$html .= '</tbody>';
		}

		//parse tfoot
		if(is_array($options['tfoot'])) {
			$html .= '<tfoot><tr>';
			foreach($options['tfoot'] as $foot) $html .= '<td>'.$foot.'</td>';
			$html .= '</tr></tfoot>';
		}

		$html .= '</table>';

		return $html;
	}
}