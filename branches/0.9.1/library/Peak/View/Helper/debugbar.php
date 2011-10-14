<?php
/**
 * Graphic version of Peak_View_Helper_debug
 *
 * @author  Francois Lajoie
 * @version $Id$
 * @uses    jQuery, Fugue icons, Peak_View_Helper_Debug, Peak_Chrono  
 */
class Peak_View_Helper_Debugbar extends Peak_View_Helper_debug 
{
	
	/**
	 * Display a bottom bar in your page
	 */
	public function show()
	{		
		//files included				
		$files = $this->getFiles();		
		//print_r($files);
		$files_count = count($files['app']) + count($files['peak']);

		if(Peak_Chrono::isCompleted() || Peak_Chrono::isOn()) { $chrono = Peak_Chrono::getMs(null,4); }
		else $chrono = 'n/a';
		
		$sid = session_id();
		if(!empty($sid)) {
			$_SESSION['debugbar']['chrono'][] = $chrono;
			/*unset($_SESSION['debugbar']['chrono']);*/
		}
		
		//print css
		echo $this->_getCSS();
		
		//print js
		echo $this->_getJS();
		
		//debug bar html
		echo '<div id="pkdebugbar">
              <div class="pkdbpanel">
               <ul>
                <li><a class="">PK v'.PK_VERSION.'/PHP '.phpversion().'</a></li>
                <li><a class="clock pkdb_tab" id="pkdb_chrono" onclick="pkdebugShow(\'pkdb_chrono\');">'.$chrono.' ms</a></li>
                <li><a class="memory">'.$this->getMemoryUsage().'</a></li>
                <li><a class="files pkdb_tab" id="pkdb_include" onclick="pkdebugShow(\'pkdb_include\');">'.$files_count.' Files</a></li>
                <li><a class="variables pkdb_tab" id="pkdb_vars" onclick="pkdebugShow(\'pkdb_vars\');">Variables</a></li>
                <li><a class="registry pkdb_tab" id="pkdb_registry" onclick="pkdebugShow(\'pkdb_registry\');">Registry</a></li>
                <li><a id="hideshow" class="hidebar" title="show/hide" onclick="pkdebugToggle();">&nbsp;</a></li>
               </ul>';
 

		//chrono
		echo '<div class="window vsmall" id="pkdb_chrono_window">';
		echo '<h2>Chrono</h2> Current: '.$chrono.' ms<br />';
		if(isset($_SESSION['debugbar']['chrono'])) {
			$nb_chrono = count($_SESSION['debugbar']['chrono']);
			$sum_chrono = array_sum($_SESSION['debugbar']['chrono']);
			$average_chrono = $sum_chrono / $nb_chrono;
			echo 'Number of request(s): '.$nb_chrono.'<br />';
			echo 'Avg: '.round($average_chrono,2).'ms / 1 request';
		}
		echo '</div>';
		
		//files included		
        echo '<div class="window" id="pkdb_include_window">';
        echo '<h2>Files information</h2>
              '.$files_count.' Files included<br />Total size: '.round($files['total_size'] / 1024,2).' Kbs<br />';
        echo '<h2>'.count($files['app']).' Application files:</h2>';
        foreach($files['app'] as $appfile) echo $appfile.'<br />';
        echo '<h2>'.count($files['peak']).' Library files:</h2>';
        foreach($files['peak'] as $libfile) echo str_replace(LIBRARY_ABSPATH, '', $libfile).'<br />';
        echo '</div>';

        //variables
        echo '<div class="window" id="pkdb_vars_window">';
		$views_vars = htmlentities(print_r($this->view->getVars(),true));
		echo '<h2>VIEW</h2><pre>'.$views_vars.'</pre>';
        if(!empty($_SESSION)) {
			$sessions_vars = htmlentities(print_r($_SESSION,true));
			echo '<h2>$_SESSION</h2><pre>'.$sessions_vars.'</pre>';
        }
		echo '<h2>$_COOKIE</h2><pre>'.print_r($_COOKIE,true).'</pre>';
        echo '<h2>SERVER</h2><pre>'.print_r($_SERVER,true).'</pre>';
        echo '</div>';
        
        //registry
        echo '<div class="window" id="pkdb_registry_window">';
        echo '<h2>'.count(Peak_Registry::getObjectsList()).' registered objects</h2>';
        foreach(Peak_Registry::getObjectsList() as $name) {
        	echo '<strong>'.$name.'</strong> ['.Peak_Registry::getClassName($name).']<br />';
        }
        
        foreach(Peak_Registry::getObjectsList() as $name) {
			$object_data = htmlentities(print_r(Peak_Registry::get($name),true));
        	echo '<h2>'.$name.'</h2><pre>'.$object_data.'</pre>';
        }
        echo '</div>';
        
        echo '</div><!-- /pkdbpanel --></div><!-- /pkdebugbar -->';
	}
	
	/**
	 * Get CSS style for debug bar
	 *
	 * @return string
	 */
	private function _getCSS()
	{
return '<style type="text/css">
<!--
/* peak debug bar */
#pkdebugbar {
 position:fixed;
 bottom:0;
 left:0;
 height:28px;
 color:#000;
 margin:0;
 padding:0;
 font: 11px/1.4em Lucida Grande, Lucida Sans Unicode, Courrier new,sans-serif;
 z-index: 255;
 border-radius:0 12px 0 0;
 -moz-border-radius:0 12px 0 0;
 -webkit-border-radius:0 12px 0 0;
 -o-border-radius:0 12px 0 0;
}
#pkdebugbar .pkdbpanel {
}
#pkdebugbar ul {
 list-style-type:none;
 padding:0;
 margin:0;
 background:#eee;
}
#pkdebugbar ul li {
 float:left;
 border-top:1px solid #bbb;
 border-right:1px solid #bbb;
}
#pkdebugbar .gradient, #pkdebugbar ul li, #pkdebugbar {
 background:#eee;
 background-image: -moz-linear-gradient(100% 100% 90deg, #ccc, #eee) !important;
 background-image: -webkit-gradient(linear, left bottom, left top, from(#ddd), to(#eee)) !important;
}
#pkdebugbar ul li:first-child a {
 text-shadow:1px 1px 0px #f5f5f5;
}
#pkdebugbar ul li:last-child {
 border-radius:0 10px 0 0;
 -moz-border-radius:0 10px 0 0;
 -webkit-border-radius:0 10px 0 0;
 -o-border-radius:0 10px 0 0;
}
#pkdebugbar li a {
 display:block;
 line-height:28px;
 padding:0 22px;
 margin:0;
 text-decoration:none;
 color:#000;
 background-position: 8px 5px;
 background-repeat: no-repeat;
 outline-style:none;
}
#pkdebugbar .current {
 background-color:#ccc;
}
#pkdebugbar a.pkdb_tab {
 text-decoration:underline;
 color:#0000EE;
 cursor:pointer;
}
#pkdebugbar li a {
 background-position:10px 7px;
}
#pkdebugbar li a.clock {
 background-image:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAmRJREFUeNqkU0trGlEUvjOOKVHHUaO0DpZIV32FxMCEvCSJuAhtwO5CQpJdsskuG7f9H9mZgiBdKKQluywiErSESGlxVQjUXusjdHxMxGfOmTrDlC666IHv3jvn3u+7Z845lxkOh+R/jMMhEokQlmWJyWQiDMPwgOfg9gGE0TkZ8B0u+wpo9Pt9MhgMSCqV+i1gsKc2u31tLhhcm5ufl7wejxedtFKh2cvLXPbi4rwhy+fgKvwRgUZ2eTxvtg8ODn1er89hsZDP19dkamaGTIqiX1hf9z+bnl6IHx8LZUqTmgiLA4Rlt/J8eHt//9Dpcvns4+Oq4lUup6ujD/d24AKIMowcXQD+6aUUDK7yDofPMjZGWIZRSe12WxdAH+7Z4MziysoqcnSBXq/nnwoEJFzX7+7ITbWqolAo6GsE7mHyApIkIUfPAXw4BYfjoXbbpNutzo1GQ1+r54BcvL0leBY5RgEWyzgwkF+FwyQUCpHdzU3C87wKm81GZpeWyMLyssoxCshNWS5bBOGxdls8kSA7W1vkXTyuR4AtJ3c6RKnXy8jRc9Dtdm8+ZTJ5DhoJw0QTXC6VjLMGSCB5ANXIptN55BirkP+QTKY7zSbtjwQ0EaNhAtl2m75PJNLI0QVg49fPUunsbTR6Itdq1MSyf/U8+hRZptGjoxNK6Rly0M/gY2KwxtB5Vqt1VhTFyO7e3uLrjY0Xj0RxAjuiRGnt4+npl1gslikWi6lWq3WlKApRuSMBTOYEwM1xnNNsNj+BhyUaHxOE/AP++9soeRVAFbg9TQBjtozA/eMF9wEtgALcwb0AAwCoRifZl32HrAAAAABJRU5ErkJggg==");
 padding-left:30px;
}
#pkdebugbar li a.memory {
 background-image:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAjVJREFUeNqck8trE1EUxr95ZZpUzeRZaUWkiCB1IURx6z/gwoV7cVEQRARx5cIiuHYjuPVfEHQv7mx0EXRTEemiSdOmedJkHvfluXfaNOmuDhzuYeZ83/mde+darx6s3QOwjP97Wq5UamXjw5f3QrIzKR3bw8bDu49dKZXVbm5DiHmDJz+AXxPf5DdyMd7VThk4HrSWDKRdrF4EY+Fcwc/DIW7dTlXf63UE5fzcd8/LQmtdIZXdae2AnyJQ0kev18NgMMBa4RPeND6b91XrMu5Xn8ElAq0lA2kHlSqSZJ5AiZHuAKUUpBCo1VKazc06LhSLyGSyEIZASCJoGoLRo5ewGr/hXF+FXH9rDHRQDbrdrqERnPK9vZRApAZOUK4gJoJ+Ywt3XqybTk/5Nj6KqykB41QsUjMmcL5QgK8JSGtG6LRb4JyBQ+HwawPhty3w19dORFxMc055b38fruulI3ByyZfKiOMQB2TA4gQMVMyYEZngaUgahRPBuSCA72ehtdrAPmjvGgJGBkkcI4EEC8MZbE7FNIYUYIlAv9MxBPx4D/KlEqIoxK42iCIyoI4xO8Ge2QOecCzm81hYONqDhHFP76om0J1TAgE+CafHKGYMNMGQTmRMBFrrjsZhrrS0ZAz+kIFyXbilADyMYNs2zeojHE1gWZbJx6MIul6PoLXWzWXned63VvXRVfri0vGP1MytoLV4dEm9FpBpTn+yK0NnR6/DWP21aM1QBBTuGa8ypxj8E2AAy9+NOgeh/UsAAAAASUVORK5CYII=");
 padding-left:30px;
}
#pkdebugbar li a.files {
 background-image:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAilJREFUeNqMk89rE1EQx2d33/6isZsURGmpOTRRiA200FJLKHgR9tBC/4aCePA/0P+hJ0V7FVrBswTai14U+8NbFUFLgocmSLKaNJvd7bpvnXntLjWE6oPhvZ3Z+c5n581K8tgY3KxUYHp2FiRJAtd1QZZl0HV9EZ/LcMmK47jB7LU1kTAQWLy/svLo3tzcMo/jocl9328/3thYZ4MBrAydbrd8Z2ZmudHpDE02DQNeVKuvv9Trr9jFqriVFUUBXdOWtvf2wHEcYIxBFEVgmiZcGRmBW/k8cPS9rFbfq6r6jf0vshcEsH1wABKeyXq9nvAzHwNU+V/Iu4eHoGPldrsN1ugonCQCiVIXhU48b6gAIX9vNuHu/DzRAsOm0239JUBYtVoNLMsSwRuTk6kAxQLfh99hCOHpKTC8+n6/fyaQoMg4A8ViUTQsl8uBcuFqKUZXfQ39tFQ8k6AQSFHQSQTZbBZarRZwzlOrLCyIpiVCCu5xEKgiL0XBQKFQEAlEQCQWNi+JSeinhhuaBoqKuZybgBQsQVHPCahJ1AcSvjo1lcaymQxcR2Ea92dbW2/B877C8bFJH07vPGzs7z+h6glmuqNpWDFTKu3InH8Uc+O6n3mz+Q6PvxiilFZte+nT0ZF4kRBNMsQ3cawT5DgMd6N6/XkyuGi+sNu2vf7DcX5GnMfDDCczfrq5+QbGx1dppgZnRJInJh5IhpG/9Lc9Q/6AR+fc0vVHgAEAIioXj94na3YAAAAASUVORK5CYII=");
 padding-left:30px;
}
#pkdebugbar li a.variables {
 background-image:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAlRJREFUeNqMU89rE0EU/naygUYbtAj+CgURUmqqoqZCKBUh9SpevAlCbz35H9hCEPFchDZQEANREPWmIBWhEDVShcUgpLUXaTE2xWRtEjdpdmd9b5JtFhXxwduZ2TffN99780a7Oj0NNk3TLtNwBP+2V1LKNek4cF0XxVwOOk/YaDyamZmZ36Fgm11KOBTz4oZhILO4OMVnkX/mf8Pj4xDM1nVNMqjdVr7TbMJqNFCv11Gr1dT4OJWap30ThI16knSHwF3TmFrQNxgI9ETbNmwirlQquJvNIhmLzc1mszcpcksRyB6BYLBQNPiDJJlMqnTC4TBeGEZ1V4HtV+AjSBtlpD+WqTjA9dh+XBvep5QoPkq3lwKx/54CW6awhdHR82r+6P0yJk8eUEr4EML8laCTghAAnWRXq1gpFjsnmib6gkE1D3QIxC7I5iJ1XDBteWEBn8bGIBo/oROInedcEybhkfd6OD+BqsEGEZzeE8GNbwUMPXuCwJeHOB55jtu5C8hv3EeQFPJeD6fznXtqWEGLSNzoYUw667j0vYR756o4c/YEReJ4vfwAE9Ep7hPRuwVfDViBRZOttwW0zW1UwzoatT6srqyqDWalpdrQ9tfgR6nUS4F+9MfjeLe3jQ+RELaGBqkbJURAKLfqnVvyUmCsvra0hMFEAqGBAZ0ZL6bTqmEkvwNaP527gjcvv4KfxKljcaWAWly36JbW83mlIkR+8FAikXJ9RgSuI6VLzePS43LpkblN23ZbtOa9jGGs7hFsb26a/SMjd/AfJi3L7BLYvwQYAF9CY3LV2hmVAAAAAElFTkSuQmCC");
 padding-left:30px;
}
#pkdebugbar li a.registry {
 background-image:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAlRJREFUeNp8U11IU2EYfs46cxtzjFqNfm1TsKRmxMTSQC/K7KIguoisSLCLoAsvugi8SbrqUrTddOEo6iKisJpY8y6tZbGCGjkZojNym25Mtp3T5jw/fd/R/ZwYe+HhfD/v83zP+37fYVydUIJhUIxFQ1NN2HjkPBnu2lqK2/hf4/ZsMF/IkeXNL1ui4RGBkw52xIJsc3eX9WRf/x46n3GPRP94X96DGcJW7jeCW8rBDzuLAv6zl/ucgUAA+3pHodfXwGyuVTZSKQ65XB7LT27C4XBg8oWbCrQoDkSpZKHx9ii+PH2GqSkvIZthMpmg1WrB8TzSqRSsHXfQ2HMF7567ixxWFEsCtKyr169hcWEBlcJeX6/klHNUDkTSGUmSUGezVRQQyZ6s0aDAsS0zagcSERAkCdWCJddFOWdCDKQ0mQuS+gShXLFCMERgO7et1pKWxmLAsEYg+QXQEqhANUSiUUT0u032wcGLceA+KwjqHmxUKYFP+PD9RxYDvvm93Q0NQz2y/F7tYKuESkivTuN3YAgXTnzFpWP7o/5c7jGhTLIbotpBpR5kkzOIzo6grdUJz8RnHLUkM1lO9zNuATRL3H89oC7KwCU+ITI7rJBfez7iUNtdrK/KmWZDDn4dEXgVAcJEZD2/eY20jAL45DxiQRfaW1sw5pnGgeP92HmwQ8mlHMrVEIeGtyuwJjPkTsmzNRiNRazMjeN0lwtvJnw4fGoAdU3nlByaSzmUS/9GA4F1iUfohkmnqt2bh7O3/cFaOoz438SHTGF9TUaIcgiEfwIMAGZKSabiyMY3AAAAAElFTkSuQmCC");
 padding-left:30px;
}
#pkdebugbar li a.hidebar {
 background-image:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAYFJREFUeNrEkzFLw0AUx1/SpE02C0UXF6EgLXQxxc3BSUWsm1N1qVBwkIyOrgoFwY+gW0E36SQ4OIhmMIN+ACelWi1terlcLr5rTTE2oNDBB/+85HL/X+7du0hBEMA4IcOYMTZACW9y+7eQ1DRI6Xo/K8lkGYcXsMSqeP/RbA5NAedwv1eIAr4HmsqMUjN8Jo7z9xKE2XNdc2tt2qCEgNPpAPO830sIzWgydzYLRqvFwO31xPBdzEcsTNUIgHO+jAZztzJv2HYbdD0BpcWsIbrMeQAiCymKBPULe3QFpNsdZOIDYz44LgfH49B1CBCXAvVYH5RKqcO50RX4fsOjNHN41DBLlSXDevXg6ezSiqtbkiRrBNC+OQF5MncK6SzUj8/Nme0Nw8XdV1S1KMnRvWbPD3hdHcDCo4zUAXEqD4n8elnOzPbbSK8Oiv7LY1yrYwE6Ki2UMCoroE3M+de1WuhBdVBvqHf0sTiAitK+QD8PmZhIUaK3BH08Avi3n+lTgAEAiza4dOMU/9wAAAAASUVORK5CYII=");
 padding:0 15px;
}
#pkdebugbar li a.showbar {
 background-image:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAYdJREFUeNrEUz9IAmEUf6dlpqAQUksNDQ1NQR/NEdSSU1uEW0OrW2tjo0OLQbQcBNFik1C0tGpgglI0SpCcmXZ/v7vvu953nZWnQeDQgx/vce+93/vzvZNc14VRJAQjysgEYz1j6bAKUuibL5lKeVqSpDyqW4dSmZomWIYBQtcPVvoJgqJ1OhCNxYRJBPxdyX8ewbFt0FXVq5ZJzxLbsrJIkvl1BHTmgXPy08k4B4ZErZYNO+l5cnpRywY7+SLA2cju9jJRVQdEjAgUmnMXFMWERoPB1voCObusZDFcQRT7CExNA8viQCn3kqnDQbds0AwT2l0d3jULujThxQ0dATsoHx1fDd1HcnONxGckuC7clPFVcuORSHHYDvY4Y32JLu4Al1nqRuPQlM+9ZGg/yUazjt6Nz2funTI6ByqHpxchsrpfEjZXHnKsVpCdl1qv4MAdhBEJxJSvQwwr0efqI5hvd6x8co/f5hCvCF1wBDsQNzGBmPR1sCUxn4kwELbrJ0r//jd+CDAARWTMh3/g7aIAAAAASUVORK5CYII=");
 padding:0 15px;
}
#pkdebugbar li a#hideshow {
 cursor:pointer;
} 
#pkdebugbar div.window {
 display:none; 
 position:absolute;
 bottom:28px;
 left:10px;
 right:10px;
 width:590px;
 height:420px;
 overflow:auto;
 padding:6px;
 border:1px solid #ccc;
 border-bottom:0;
 text-align:left;
 background:#f1f1f1;
 background-image: -moz-linear-gradient(100% 100% 90deg, #ddd, #f1f1f1) !important;
 background-image: -webkit-gradient(linear, left bottom, left top, from(#ddd), to(#f1f1f1)) !important;
 border-radius:6px 6px 0 0;
 -moz-border-radius:6px 6px 0 0;
 -webkit-border-radius:6px 6px 0 0;
 -o-border-radius:6px 6px 0 0;
}
#pkdebugbar div.window.vsmall { height:100px; }
#pkdebugbar div.window.small { height:200px; }
#pkdebugbar div.window.medium { height:300px; }
#pkdebugbar div.window.large { height:500px; }

#pkdebugbar div.window h2 {
 margin:10px 0 10px 0;
 font-size:18px !important;
 text-shadow:2px 2px 0 #ccc;
}
-->
</style>';
	}
	
	/**
	 * Get JS tag
	 *
	 * @return string
	 */
	private function _getJS()
	{
return '<script type="text/javascript">

//check for jquery
if (typeof jQuery == "undefined") {
	var scriptObj = document.createElement("script");
	scriptObj.src = "http://ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js";
	scriptObj.type = "text/javascript";
	var head=document.getElementsByTagName("head")[0];
	head.insertBefore(scriptObj,head.firstChild);
	jQuery.noConflict();
}

function pkdebugShow(id) {
	var target = "#" + id + "_window";
	var id = "#" + id;

	if(jQuery(id).hasClass("current")) {
		jQuery(id).removeClass("current");
		if(jQuery.browser.msie) jQuery(target).hide();
		else jQuery(target).slideUp("fast");
	} else {
		pkdebugCloseAll();
		jQuery(id).addClass("current");
		if(jQuery.browser.msie) jQuery(target).show();
		else jQuery(target).slideDown("fast");
	}
}

function pkdebugCloseAll() {
	jQuery("#pkdebugbar .window").hide();
	jQuery("#pkdebugbar .pkdb_tab").removeClass("current");	
}

function pkdebugToggle() {
	if(jQuery("#pkdebugbar li a#hideshow").hasClass("hidebar")) {
		pkdebugCloseAll();
		jQuery("#pkdebugbar li a").hide();
		jQuery("#pkdebugbar li a#hideshow").show().removeClass("hidebar").addClass("showbar");
	} else {
		jQuery("#pkdebugbar li a").show();
		jQuery("#pkdebugbar li a#hideshow").removeClass("showbar").addClass("hidebar");
	}
}
</script>';
	}
	
}