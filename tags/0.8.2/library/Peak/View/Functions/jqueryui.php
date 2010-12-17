<?php

/**
 * @deprecated 
 * Misc functions for transforming data into valid html jquery ui code
 * 
 * @author   Francois Lajoie
 * @version  $Id$
 */

/**
 * Put jquery code inside <script>
 *
 * @param string $code
 * @return string
 */
function jqueryui_scriptcode($code)
{
    return '<script type="text/javascript">$(document).ready(function() { '.$code.' });</script>';
}

/**
 * Transform array into html tabs code
 * 
 * @param  array  $tabs
 * @param  string $wrapper_class
 * @param  string $tabs_prefix
 * @return string
 * @example 
 * 
 *     array('tabs' => array('mytab1' => 'My tab1 title',
 *                           'mytab2' => 'My tab2 title '),
 *           'content' => array('mytab1' => 'My tab1 content...',
 *                              'mytab2' => 'My tab2 content...');
 *     OR
 * 
 *     array('id1' => 'id1 content', 'id2' => 'id2 content);
 */
function jqueryui_tabs($data, $wrapper_class = 'tabs_wrapper', $prefix = 'tabs-', $print_script_tag = true)
{
    if(!function_exists('replace')) { function replace($txt) { return str_replace(array(' ','.'),array('_','_'),$txt); } }
    
    $html = '<div class="'.$wrapper_class.'">';
    
    if((isset($data['tabs'])) && (is_array($data['tabs'])) && (isset($data['content'])) && (is_array($data['content'])))
    {
        $html .= '<ul>';
        foreach($data['tabs'] as $id => $title) {
            $html .= '<li><a href="#'.$prefix.replace($id).'">'.$title.'</a></li>';
        }
        $html .= '</ul>';
                
        foreach($data['content'] as $id => $content) {
            $html .= '<div id="'.$prefix.replace($id).'">'.$content.'</div>';
        }
    }
    else {
        $html .= '<ul>';
        foreach($data as $id => $content) {
            $html .= '<li><a href="#'.$prefix.replace($id).'">'.$id.'</a></li>';
        }
        $html .= '</ul>';
        
        foreach($data as $id => $content) {
            $html .= '<div id="'.$prefix.replace($id).'">'.$content.'</div>';    
        }
    }
    
    $html .= '</div>';
    
    if($print_script_tag) $html .= jqueryui_scriptcode('$(".'.$wrapper_class.'").tabs();');
    
    return $html;
}