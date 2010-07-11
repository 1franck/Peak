<?php

class view_helper_phphl
{
    
    /**
     * Highlight php source file #from php.net
     *
     * @param string $string
     * @return string
     */
    public function highlight($string)
    {
        /*$theme = array('comment' => '',
                       'default' => '',
                       'html' => '',
                       'keyword' => '',
                       'string' => '',
                       'bg' => '');*/
                       
        $theme = array('comment' => '#FF9966',
                       'default' => '#9BCDE7',
                       'html' => '#000000',
                       'keyword' => '#E8B56D',
                       'string' => '#CCD08E',
                       'bg' => '#1F1C1B');
        
        $default_theme = array('comment' => '#FF8000',
                               'default' => '#0000BB',
                               'html' => '#000000',
                               'keyword' => '#007700',
                               'string' => '#DD0000',
                               'bg' => '#FFFFFF');
       
        $Line = explode("\n",$string);
        $line = '';

        for($i=1;$i<=count($Line);$i++) { $line .= "&nbsp;".$i."&nbsp;<br>"; }

        ob_start();
        highlight_string($string);
        $Code=ob_get_contents();
        ob_end_clean();
        
        foreach($default_theme as $el => $htmlcolor)
        {
            $Code = str_replace('<span style="color: '.$htmlcolor,'<span style="color: '.$theme[$el],$Code);
        }
        
        $Code = str_replace('style="background-color: #fff','style="background-color: '.$theme['bg'],$Code);

        $header = '<table width="95%"><tr>
               <td width="3%" valign="top" style="background-color: #565656;"><code>'.$line.'</code></td>
               <td width="97%" valign="top" style="background-color: '.$theme['bg'].';"><div style="white-space: nowrap; overflow: auto;"><code>';

        $footer = $Code.'</div></code></td></tr></table>';

        return $header.$footer;

    }
    
}