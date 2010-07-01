<?php

/**
 * View Object TEMPLATE SHORCUT FUNCTIONS
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */

/**
 * Shorcut of $wyn->view object
 * 
 * @example view()->variablex; = $wyn->view->variablex;
 *          view()->func($param1); = $wyn->view->func($param1);
 *
 * @return object
 */
function view()
{
    return Peak_Registry::obj()->view;
}


/**
 * Shortcut of $wyn->view->helper([helper name])->[helper_func](...)
 *
 * @example view_hlp('hello')->say();  =  $wyn->view->helper('hello')->say();
 * 
 * @param helper name $name
 * @return object
 */
function view_hlp($name = null)
{
    return view()->helper($name);
}

/**
 * Shortcut of $wyn->view->$variable
 *
 * @param string $var
 * @return any type
 */
function view_get($var)
{
    return view()->$var;
}

/**
 * Shortcut of echo $wyn->view->$variable
 *
 * @param string $var variable name
 * @param string $lc  left  variable content
 * @param string $rc  right variable content
 */
function view_echo($var,$lc = '',$rc = '')
{
    echo $lc.view()->$var.$rc;
}

/**
 * Basic view function
 */

/**
 * Create rewrited url @deprecated
 *
 * @param string $controller
 * @param string $action
 * @param array $params
 * @return string
 */
function baseurl($controller = null,$action = null,$params = null)
{
    $url = ROOT_URL;
    if(isset($controller)) $url .= '/'.$controller;
    if(isset($action))     $url .= '/'.$action;
    if(isset($params))
    {
        if(is_array($params)) $url .= '/'.implode('/',$params);
    }
    return $url;
}


/**
 * Misc template html functions
 * ----------------------------
 */


/**
 * Transform css file or array of css files into meta link stylesheet tags
 *
 * @param string/array $files
 * @return string
 */
function html_meta_link_ss($files)
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
function html_meta_script($files) 
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
 * @param string $code
 * @return string
 */
function html_style_code($code)
{
    return '<style type="text/css"><!-- '.$code.' --></style>';
}

/**
 * Put js code inside <script>
 *
 * @param string $code
 * @param string $type
 * @return string
 */
function html_script_code($code,$type = 'text/javascript')
{
    return '<script type="'.$type.'">'.$code.'</script>';
}

/**
 * Put jquery code inside <script>
 *
 * @param string $code
 * @return string
 */
function html_jquery_code($code)
{
    return '<script type="text/javascript">$(document).ready(function() { '.$code.' });</script>';
}

/**
 * Transform to img html 
 *
 * @param string $img    image url file path name
 * @param string $attrs  html tag attribute
 * @return string
 */
function html_img($img,$attrs = array('alt' => ''))
{
    $img = W_TPL_URL.'/images/'.trim(stripslashes(strip_tags($img)));

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
function html_links($array,$class = '',$sep = ' | ')
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
$html_cycle  = 1;  //see cycle() method
function html_cycle($a,$b)
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
function html_table($data,$options = array())
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