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
 position: fixed;
 bottom:0;
 left:0;
 background:#eee;
 height:25px;
 color:#000;
 margin:0;
 padding:0;
 border-top:1px solid #bbb;
 /*border-right:1px solid #ccc;*/
 font: 11px/1.4em Lucida Grande, Lucida Sans Unicode, sans-serif;
 z-index: 255;
}
#pkdebugbar .pkdbpanel {
}

#pkdebugbar ul {
 list-style-type:none;
 padding:0;
 margin:0; 
}
#pkdebugbar ul li {
 float:left;
 background-image: -moz-linear-gradient(100% 100% 90deg, #ddd, #eee) !important;
 background-image: -webkit-gradient(linear, left bottom, left top, from(#ddd), to(#eee)) !important;
 background-image: url(images/linear_bg_1.png);
}
#pkdebugbar li a {
 display:block;
 line-height:25px;
 padding:0 15px;
 margin:0;
 border-right:1px solid #bbb;
 text-decoration:none;
 color:#000;
 background-position: 8px 5px;
 background-repeat: no-repeat;
 -moz-box-shadow: 0px 0px 5px #A3A3A3;
 -webkit-box-shadow: 0px 0px 5px #A3A3A3;
 box-shadow:0px 0px 5px #A3A3A3;
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
#pkdebugbar li a.clock {
 background-image:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAodJREFUeNqkU8tLG2EQn92NWjevNSbUbFca2kufiIUEKw0+CBRaIb0q6k0peJOCt9L/w5sWUkKhSbElNw8GK7GVemgp9EGV2i/GhLrmoY3Jbme2u0tKDz104Pft7uz8ft98M/Nxuq7D/5iDlng8DjzPgyAIwHGcG3EF3QrCa8apiG+42XtEudlsgqZpkE6nfwu02CWXxzMSiUZHIgMD4WAgECQnOzhguY2Nzdza2mpZVVfR9eGPDCyyLxC4NzE7O6cEg4okivAsswvnehUQz/SEIrduhy739d1MLC56C4ylLBGeFkzL43S7YxMzM3NdPp/i6ew0FD9/+giazsF+SYfsGx4Oq5IyiRtgljHi2AJ4pmvhaHTYLUmK2N4OPMcZApVKBQSM8Lh4CMkd8GqrDieaWxkcGhomji3QaDRC1/v7w/R+dHwMO8Wiga87u1BCEUL5ZxW6fQKs5X5AfzgcJk6rQJdXks5axTjv9xsol5Hkctm4qEiw+bYIFEscu4j4wVMbNZNMdicWg9HRUXjw8AlonBN0DNWBAw5bTe0mTquAWlHVguj19lpZJJJJmBwfh+eJ+3abaOTUeh1qR0cF4thHOD093Xm9vr7tIGVNM4K9Ph88TiSMpwWXJEEHdiiXzW4Tp7UL2y9SqWy9UmFNU8ASaTWaPv7khD1NJrPEsQXwx+F+Pp95tLCwrJZKTOD5v2aefDVVZQvz88uMsQxxyM/RZcLZBxEnz+l03pBlOT41PT14d2zsao8sd9NE5BkrvVxZebe0tLS+t7eXrlarW7VaDQyuKUDF7Eb4HQ5HV1tb2wWstNx6mTDl73juL2bxDhBF5DYsAcpZNOH4xw1uIqqIGnK1XwIMAPiEMNKmMDl2AAAAAElFTkSuQmCC");
 padding-left:30px;
}
#pkdebugbar li a.memory {
 background-image:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAjVJREFUeNqck8trE1EUxr95ZZpUzeRZaUWkiCB1IURx6z/gwoV7cVEQRARx5cIiuHYjuPVfEHQv7mx0EXRTEemiSdOmedJkHvfluXfaNOmuDhzuYeZ83/mde+darx6s3QOwjP97Wq5UamXjw5f3QrIzKR3bw8bDu49dKZXVbm5DiHmDJz+AXxPf5DdyMd7VThk4HrSWDKRdrF4EY+Fcwc/DIW7dTlXf63UE5fzcd8/LQmtdIZXdae2AnyJQ0kev18NgMMBa4RPeND6b91XrMu5Xn8ElAq0lA2kHlSqSZJ5AiZHuAKUUpBCo1VKazc06LhSLyGSyEIZASCJoGoLRo5ewGr/hXF+FXH9rDHRQDbrdrqERnPK9vZRApAZOUK4gJoJ+Ywt3XqybTk/5Nj6KqykB41QsUjMmcL5QgK8JSGtG6LRb4JyBQ+HwawPhty3w19dORFxMc055b38fruulI3ByyZfKiOMQB2TA4gQMVMyYEZngaUgahRPBuSCA72ehtdrAPmjvGgJGBkkcI4EEC8MZbE7FNIYUYIlAv9MxBPx4D/KlEqIoxK42iCIyoI4xO8Ge2QOecCzm81hYONqDhHFP76om0J1TAgE+CafHKGYMNMGQTmRMBFrrjsZhrrS0ZAz+kIFyXbilADyMYNs2zeojHE1gWZbJx6MIul6PoLXWzWXned63VvXRVfri0vGP1MytoLV4dEm9FpBpTn+yK0NnR6/DWP21aM1QBBTuGa8ypxj8E2AAy9+NOgeh/UsAAAAASUVORK5CYII=");
 padding-left:30px;
}
#pkdebugbar li a.files {
 background-image:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAfRJREFUeNqMU89LAlEQnn27WiFUl0ASCbp0Cgm6dulWEHXoIF26Vv9Bh4Io6B5C5yDp1K1TUGQQCAqK1wLFyLIfaz9Eaddd++bxVhaxaGAc39uZ7/tmmKcRbHJ+niamp0nTNGq32xwXcD1Kve3CcZw7p9WSucby9nankI3/IyFyvLNzaCPJdl36dhxy8D2Xy9HR+fk6p8FvOd/oQpfM+Dpzmc1SCwAuCm0ARMNhqtfrdLq7e7i0ubmh8m81TwEbmNd+Y27ifF8sUn8gIHMPkskthD3jv8wrc3N0gvNYJEJR+Fkm8yFbcIEM+7Xn8uMjVapVSiSTkqlUKtFqPE4t2xYSgKfJQ3FRJJm5GLFpWT2Zr9Np6hOCbAUguAguBOags+s69RsGDQSDZJqmZH6DitTNDekoJCgMMIBlCTgZkCKBGECoBGIQXC5iP1jJQCjUYWaFnOsp6ABoSgF1gbD5mR0PwLJ0CWB7CuSPKu4CET5mXjgGfDfN4HM+T7IP7sebASf7I8/Ez7yXSFyNTE3tv5bLX03THO8o8NZY9/T4lPiZrUYj+1UoJFR3TfH+8CD7kUOEaz2U+JlrT08VFHJnHGvGXSo1PDY7G1STJPWiSAOjyyD8yHBWzLxNNWb2Hg+v8hD6+RyMxfbpD3MbjRdVWPHf/wgwAAuqSbfOGi3pAAAAAElFTkSuQmCC");
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
 background-image:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAhRJREFUeNqMUz1oU1EUPu/38oooBEOoQ7A1oxCx1KRIZqEI2bt1EmfBwQymQzt0crCkgmCGFsShCF06OOkzKBJLhw6F0k6lBCXQmp93330/fvf5oq8xgR74ePfde853vnPPuUquWCRV0ygzNUWZ6WlSFIViywNZYJsSFoYhtY6OqHV8TAe2TfqtQoEM0yTDMJJ+MrhAY+x6NkvXJiejtTriPAp+U62+okuYPhwMicWNpaV1x3WlXFnPoyGfL8DeKIK873lzb5eXa91ejxRVpdeVyroXBCQA+f2+u0v1nZ3Hsf/evxLC8I4Q4v47BHPHIRUXGcogIcgHBOfE+33qdDq0BR/pK2MiAsi8i43S+9XVNReyZXASGpQwXScTaLfb9HJzkx7k82sH+/sPoxKE65Y+1GovPM+LAoJRNxuTlOfnyYUfm5igLdv+FR25nNulxcUnOhwGWZUhFcNKdPyjVG1QQhPSP91bWHh6WRIDX8659vcSQfINjB9vl8uVAYmG6ZRrOWBmDIaBsxgjhnWv2zUutNH3/a8/T0/19MzMyo9m81mALmRmZ1fGDRAStCKCRr0ebVipFGmp1GcHclguJ/jh4fPzszNhMlZV1f8H1oSaCwr6aBH6RIplNVSQgeTPPh5NiBkYZ4OnJ1NcAa7GsLR0+ibS3PBPThoJfwc4j9GBWF9JEElNLIYxJqEH8BguCILfAgwAON7hGa4F76YAAAAASUVORK5CYII=");
 padding:0 15px;
}
#pkdebugbar li a.showbar {
 background-image:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAhVJREFUeNqMUz9oE1EY/15e7pKAOOnQIlSwi1SIsZAo0k1IB8ut0skuSjZByGCWdmiHuBSxVAcdhAwu4qJQtxodQnBwCOpQkC5VIjdoau4u9+75+17OUI9U8uDH99677/d7378TWmsSQtBcuUznSyWzT6wlYB/4yAf2/9RqUWdnx3wUfLFUrZJlWSTT6XECt2LbYhH2V2FIg8GAPjeblKIJ1tPV1ccwJSB/9P4cIk4K5PHC7QTEIAjoGUSwv5wUSR8lI7QrNxcXtwuFAkmkkk6lyAa0UvT78JAaa2uPbtRqldjf1GQYgdYXkdPVF+vr271ej/pwDvp9Ung5RK46iigFQc/z6Dl82Jc5I4Evnc71cj6/9bDRINd1yZaSMiiqBctEA0TCltN5Wa9vQWQBKV0yKZyZmfnlOI55+X9kBncpRBdeb24+uFap3DECvudJznkSMlsL7V5YWbmL87uhgO9LLpiYkFxcXq7i3LRt+4MRQIWtnG2TBtk48oQxiYsEa6YVeyZfcJyalPJtJpttj9r44+Dg+9liceO4QfrWbt+TiOb0/PwGyLunpqZa/8yBUupJgOomV4T2Bb5fZ3JmdvY+rt5khXj/dW+P+ujW6F8YM//Dj7kcyenpuhkVz3sVue6uxnyMm0RO+wRwMkbOkOAMYpeCYF91uz1czQE/Y/BZ/Y2AQ7CBTAzrmHKE3LQYnHP0R4ABALxE7aAWYkHNAAAAAElFTkSuQmCC");
 padding:0 15px;
}
#pkdebugbar li a#hideshow {
 cursor:pointer;
} 
#pkdebugbar div.window {
 display:none; 
 position:absolute;
 bottom:25px;
 left:10px;
 right:10px;
 width:570px;
 height:400px;
 overflow:auto;
 background:#eee;
 padding:6px;
 border:1px solid #aaa;
 text-align:left;
}
#pkdebugbar div.window.vsmall { height:100px; }
#pkdebugbar div.window.small { height:200px; }
#pkdebugbar div.window.medium { height:300px; }
#pkdebugbar div.window.large { height:500px; }

#pkdebugbar div.window h2 {
 margin:10px 0 10px 0;
 font-size:18px !important;
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