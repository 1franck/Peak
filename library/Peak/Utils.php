<?php

/**
 * Wyn utils class
 * 
 * @desc     Content all sort of usefull functions found here and there
 * @author   Francois Lajoie
 * @version  $Id$
 */
class Peak_Utils
{
    
    
    public static function strip_tags_attributes($string,$allowtags=NULL,$allowattributes=NULL){
        $string = strip_tags($string,$allowtags);
        if (!is_null($allowattributes)) {
            if(!is_array($allowattributes))
            $allowattributes = explode(",",$allowattributes);
            if(is_array($allowattributes))
            $allowattributes = implode(")(?<!",$allowattributes);
            if (strlen($allowattributes) > 0)
            $allowattributes = "(?<!".$allowattributes.")";
            $string = preg_replace_callback("/<[^>]*>/i",create_function(
            '$matches',
            'return preg_replace("/ [^ =]*'.$allowattributes.'=(\"[^\"]*\"|\'[^\']*\')/i", "", $matches[0]);'
            ),$string);
        }
        return $string;
    }

            
    /**
     * Scan directory vars
     *
     * @var array
     */
    public static $scan_stats = array('files_found' => 0,'folders_found' => 0);
    
    /**
     * Scan directory
     *
     * @param string $dir
     * @param bool $scanfolder
     * @param bool $scanfile
     * @param array $ignorefolder
     * @param array $ignorefile
     * @return array
     */
    public static function scan($dir,$scanfolder = true,$scanfile = false,$only = null,$ignorefolder = array('.','..'),$ignorefile = array('exe','bat'),$getstats = false)
    {
        $it = new DirectoryIterator($dir); $result = array();
        while($it->valid()) {
            if($scanfolder) {
                if(($it->isDir()) && (!in_array($it->getFilename(),$ignorefolder))) { 
                    $result[] = $it->getFilename();
                    self::$scan_stats['folders_found']++;
                }
            }
            if($scanfile) {
                if(($it->isFile()) && (!in_array(pathinfo($it->getFilename(),PATHINFO_EXTENSION),$ignorefile))) {
                    if((!isset($only)) || (pathinfo($it->getFilename(),PATHINFO_EXTENSION) === $only)) {
                        if($getstats) {
                            $result[$it->getFilename()] = stat($dir.'/'.$it->getFilename());
                        }
                        else {
                            $result[] = $it->getFilename();
                        }
                        self::$scan_stats['files_found']++;
                    }
                }
            }
            $it->next();
        }
        return $result;
    }
    

    /**
     * Object sorting (A-Z)
     *
     * @param object $data array of object
     * @param string $key array key sorting index
     */
    public static function objectSort(&$object, $key)
    {
        for ($i = count($object) - 1; $i >= 0; $i--) {
            $swapped = false;
            for ($j = 0; $j < $i; $j++) {
                if ($object[$j]->$key > $object[$j + 1]->$key) {
                    $tmp = $object[$j];
                    $object[$j] = $object[$j + 1];
                    $object[$j + 1] = $tmp;
                    $swapped = true;
                }
            }
            if (!$swapped) return;
        }
    }
    
    /**
     * Object reverse sorting (Z-A)
     *
     * @param object $object
     * @param string $key
     */
    public static function objectRSort(&$object, $key)
    {
        for ($i = count($object) - 1; $i >= 0; $i--) {
          $swapped = false;
          for ($j = 0; $j < $i; $j++) {
               if ($object[$j]->$key < $object[$j + 1]->$key) {
                    $tmp = $object[$j];
                    $object[$j] = $object[$j + 1];      
                    $object[$j + 1] = $tmp;
                    $swapped = true;
               }
          }
          if (!$swapped) return;
        }
    } 
    
    /**
     * Get file size
     *
     * @param string $handler (file + realpath)
     * @return string
     */
    public static function fsize($handler)
    {
        if(!is_numeric($handler)) {
            if(file_exists($handler)) $bytes = filesize($handler);
            else $bytes = 1;
        }
        else { $bytes = $handler; }
        
        if ($bytes >= 1099511627776) {
            $return = round($bytes / 1024 / 1024 / 1024 / 1024, 2);
            $suffix = 'TB';
        }
        elseif ($bytes >= 1073741824) {
            $return = round($bytes / 1024 / 1024 / 1024, 2);
            $suffix = 'GB';
        }
        elseif ($bytes >= 1048576) {
            $return = round($bytes / 1024 / 1024, 2);
            $suffix = 'MB';
        }
        else {
            $return = round($bytes / 1024, 2);
            $suffix = 'KB';
        }
        $return == 1 ? $return .= ' ' . $suffix : $return .= ' ' . $suffix . 's';
        return $return;
    }
    
    /**
     * Remove extension form filename string
     *
     * @param string $filename
     * @return string
     */
    public static function fileremoveext($filename)
    {        
        $ext = pathinfo($filename,PATHINFO_EXTENSION);
        return str_replace('.'.$ext,'',$filename);
    }
    
    /**
     * Reverse of nl2br
     *
     * @param string $str
     * @return string
     */
    public static function br2nl($str) { return preg_replace('/<br\\s*?\/??>/i', '', $str); }
    
    /**
     * Transform rgb value to html hexa color
     * 
     * @link http://www.anyexample.com/programming/php/php_convert_rgb_from_to_html_hex_color.xml
     *
     * @param integer $r
     * @param integer $g
     * @param integer $b
     * @return string
     */
    public static function rgb2html($r, $g=-1, $b=-1)
    {
        if (is_array($r) && sizeof($r) == 3)
        list($r, $g, $b) = $r;

        $r = intval($r); $g = intval($g);
        $b = intval($b);

        $r = dechex($r<0?0:($r>255?255:$r));
        $g = dechex($g<0?0:($g>255?255:$g));
        $b = dechex($b<0?0:($b>255?255:$b));

        $color = (strlen($r) < 2?'0':'').$r;
        $color .= (strlen($g) < 2?'0':'').$g;
        $color .= (strlen($b) < 2?'0':'').$b;
        return '#'.$color;
    }
    
    /**
     * Transform html hexa color to rgb value
     *
     * @param string $color
     * @return array 
     */
    public static function html2rgb($color)
    {
        if ($color[0] == '#')
        $color = substr($color, 1);

        if (strlen($color) == 6)
        list($r, $g, $b) = array($color[0].$color[1],
        $color[2].$color[3],
        $color[4].$color[5]);
        elseif (strlen($color) == 3)
        list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
        else
        return false;

        $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

        return array($r, $g, $b);
    }
    

}