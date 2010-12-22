<?php
/**
 * Peak Framework Application Creator
 * 
 * @descr   This is a all-in-one file. This file SHOULD BE DELETED/NOT USED in PRODUCTION environment.          
 * @author  Francois Lajoie
 * @version $Id$
 */

/**
 * PEAK FRAMEWORK PATH
 */
define('PEAK_PATH', dirname(__FILE__).'/library/Peak');


/**
 * check requirements
 */

error_reporting(E_ALL|E_STRICT);

//Peak library
if(file_exists(PEAK_PATH.'/Core.php')) $peak_library_found = true;
else $peak_library_found = false;

//php version
if(version_compare(PHP_VERSION, '5.2.0') >= 0) $php_version = true;
else $php_version;

//apache
if(function_exists('apache_get_version')) {
  $amodules = apache_get_modules();
  if(in_array('mod_rewrite', $amodules)) $apache_mod_rewrite = 2;
  else $apache_mod_rewrite = 1;
}
else $apache_mod_rewrite = 0;


/**
 * form default data
 */

$form = array();

$form['app_path']         = $_SERVER['DOCUMENT_ROOT'];
$form['public_path']      = '';
$form['public_index']     = 'off';
$form['public_htaccess']  = 'off';
$form['app_configs_file'] = 'configs.ini';
$form['app_controllers']  = 'index,error';
$form['app_modules']      = '';
$form['app_bootstrap']    = 'on';
$form['app_front']        = 'off';
$form['app_configs']      = '
[all]
php.display_errors = 1
php.display_startup_errors = 1

[development]

[testing]

[staging]

[production]
php.display_errors = 0
php.display_startup_errors = 0';


/**
 * images base64
 */

$img = array('pagetitle' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAjJJREFUeNqkU0trE1EYPXfunUwm0xgrmodJIyltaA0iPkDEhZuI/yB/wbUSEJeuCi78Af0LbszO0IULX7ELQWmpFfMyrVqbRyfJpM08vXdqhW6E2Dsc7mW43/nO+e73Ec/zcJLFCoXCLN9vcIQnjB1wvGeO49wsFouPotFoznFs2LbDYcGyxNmG67o4FElBCPPBWACdTmd9efnpEuMXThmGkavVav5lAU7q419njlypVNKYLMtuPp//L//lcpkwLpMcyjxezLEDvN02sdI0Uddd6GMXEUVCJiLhzoUAbqUUYdEnkI5kHa1W38GTig6L/7qWUHA1JiNEgZlECN96Fp5/HuLZui4IpL8EAn6wbuHxy10sxELIpTRkkmFstgzsGC7OHtjIJoLIxoN490XHw0v3bzPLsiRRbYGx7WHpRQPZ5DTUIEXktMopJcBzcUajcLjLwYgnkikWMhF0lfMxZpomFdkFwavNLnr6EGRqCqoJxPs2KhttWDzwYjICmylYeVPF/FwcWkSDN+hKgsC3wJWg/KGB1Lk46ga3tO/ix+oWggpDgEpoD9oYVOroD/YxnUpj43sX0Nv0mIKvzR1cn7uMcb2NrjmGqiqg3DclBO7YxGw8hsX5GKq/DvBprQlPEPAmkgm/wPsB/b6B16tr0NKLGO1t+U9JxEd4GXiSUfUnGi0d0Zk06tu78IY9SsLh8D1KaVq8wN6VB3cn6qS9xkfODfXPILEJG9EWA0VOOs6/BRgAbW1C3QWZUtUAAAAASUVORK5CYII=',
             'newapp'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAYFJREFUeNqkUzFLw0AY/S53EUpJQSixFHVwNIK6WAq6lE5uCvYXCP6BLN06KG6Ca/EPOHbsJnSqDlptK4JUlA4OzdDWDCa5i/ddq1trox+8fA9y38t7lzsShiH8p1ihUFiRPSNhRJwdStQZ5zxr23bRNE2L8wCCgEv44PvIAxBCwMgkBUKYAmNz4DhOq1w+O2VyQcJ1XavT6ajFCCmqMI1LWJVKJc50XRf5fP5P+avVKmHSJhnZjLaZhBCMqAS0b1tRStM0FNB+BBBRilI6EvB9X8PdRkQpjIyzzPM8il+fRcB+KsHdoAUbCQvO104AZ1FARZBqvwrcOg+wtZOB61pdRcbZmRwUH4/hftBWvM/7EHoCcld78JbrHjB5iHT8JfI8TBRo9lqQzC0o3gt6kNw2FW8/N5eIYRhHckeXp1n/2Be7YhFSyFcP11Pti8a7evHK69hiEiiZ/hWleCP9YoXYxzMxKh8Y3h3frsnIsiHMh5vDmgPwKW6AkkvoiuBLgAEAMBv5dCCQwMYAAAAASUVORK5CYII=',
             'folder'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAaFJREFUeNqkU88vA0EU/mZ2WiElfobERfwBLiIhcfA/OLkicXSU4OJQcSc9ceQiEkcu4ibiUhWkxKb1o6qyqVLbre7OerMqWqlkpS/59n2T9743M2/2Mdd1UY+JkxVWM8AZBsiN1wjtSBex730FYYrQ9zuLkjA4JxdzhQKypolpvQOGYWL9NMQpPETYUHnsKIzw8OzuPBwHrivhyko4X17FypwxhujmzDJpF7wTOBKaEsuPYpVQVog9XvaaFoCnIQvQl6tF9c5/i79PojRB4fUJwqaFtG0Y8UMU354BVYw69AVJa/Lyh3PO1c6jpF2iQyQEFRVWLg2bknpGJnw9Xe/Y5PB7Ru++2J5fFTZdK288QGtqg5VJw89/wRjHc/z43rJxrgqIbEpHV/8gbGqkH9OCDUjfnKVNC5eiJCHyuVf0sIDvAk7Jeksl9SeN415Q/wJaYztsy6ROS18FjFT8MVfANVFXEGkJNXfSk3Bwjfu4P8OdfvVydovY2h49YzSB5OtW5OA/A6RncB3Zh060VU1SUJHyXPg1h2ARiqzecf4UYAAOUhKfHcZJBAAAAABJRU5ErkJggg==',
             'config'    => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAoJJREFUeNqkU01oE1EQnk02iTFQE7QihUKRkKTF1iU9+FdQCoWYgAcPegkIeiiIWiHgwUvpQXs1Ggo99OYlFwUhWAhYhZJWUmhMxJbYYk1LFDcmJraSv911vjQbevPgg9kZ5vu+eW9n3hM0TaP/WSI+gUCADAYDmUwmEgSBUNRoNJ5jaKjNSyuKsqRjjUaDVFWlWCy2X0BfDJ5nd5r9KxZI0Wh0BuRgMHibcznGrrD/wD6hawwHxBdcLte12dnZGYfDcYOFhkJBpnL5F3Y0IAcMHHB1nYAj+Xw+xHeZ8FSWf1BPTw+trqY2JElyAkilUhsej8dZKhWpu/s4jY+P3+P0s/n5+f0TVCoVqlarL0Oh0KTZbCZZlmlgoN+pqgrBEO/u/iZg4IALTecX+BQX6/X69Xw+v8e7bYqiSMvLy+t+f2AGhhg5YOCAC43+7+T1eh+srCS1hYU32tJSQkun09rg4NA0TwLTIMTIAQMHXGigbU2hVqsZq9UaNZsKKYrKoxRZKDYwKizEyAEDB1xoOk3kzo6xP4PExMT9WyMjl/q2t7+npqYevkBucvLx1d7eE9Li4tutcPjJXEsoCO+z2WxcP0GcC3zmDt8ZHj7bVyyWyO32SLHYOwl4ufyTdna+ELCuriN2nlSEC2x1mshdRZGbkchcSJaLfCOtFI+//prLbRIMMXLAwAEXmk4T+ZLALo+Ojj1PJtc1t7s/bLfbHyUSGQ2GGDlg4IALTesd6Y8JY7JarX6bzTZtsVhOwq+tfdMymZx2MAcOuPrmrSYKaDHRUbZjbIcA8sM6xQ9sADFP4xNf54/t21tnk9kKrG3qBdCLw20T//GCFbY9tj+sVf8KMAACOoVxz9PPRwAAAABJRU5ErkJggg==',
             'files'     => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAfRJREFUeNqMU89LAlEQnn27WiFUl0ASCbp0Cgm6dulWEHXoIF26Vv9Bh4Io6B5C5yDp1K1TUGQQCAqK1wLFyLIfaz9Eaddd++bxVhaxaGAc39uZ7/tmmKcRbHJ+niamp0nTNGq32xwXcD1Kve3CcZw7p9WSucby9nankI3/IyFyvLNzaCPJdl36dhxy8D2Xy9HR+fk6p8FvOd/oQpfM+Dpzmc1SCwAuCm0ARMNhqtfrdLq7e7i0ubmh8m81TwEbmNd+Y27ifF8sUn8gIHMPkskthD3jv8wrc3N0gvNYJEJR+Fkm8yFbcIEM+7Xn8uMjVapVSiSTkqlUKtFqPE4t2xYSgKfJQ3FRJJm5GLFpWT2Zr9Np6hOCbAUguAguBOags+s69RsGDQSDZJqmZH6DitTNDekoJCgMMIBlCTgZkCKBGECoBGIQXC5iP1jJQCjUYWaFnOsp6ABoSgF1gbD5mR0PwLJ0CWB7CuSPKu4CET5mXjgGfDfN4HM+T7IP7sebASf7I8/Ez7yXSFyNTE3tv5bLX03THO8o8NZY9/T4lPiZrUYj+1UoJFR3TfH+8CD7kUOEaz2U+JlrT08VFHJnHGvGXSo1PDY7G1STJPWiSAOjyyD8yHBWzLxNNWb2Hg+v8hD6+RyMxfbpD3MbjRdVWPHf/wgwAAuqSbfOGi3pAAAAAElFTkSuQmCC',
             'folders'   => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAeBJREFUeNqMkz1IHEEUx/+7MysSxMihYhD0EIsjhZ2doCGCpZA21QlaiLFKFdIEUqRKkcJGwcImkCbXaRUQEohaCXoQuKCCHooSdC93uczH5r29r7nzMA48dph57ze/eXPnLUwDHDyiqPL1PKTpk8Td44jy1+X808YKFcJGMWhkfDnzGsa0LxUCux9m3/JUtu4xRBv4UH8RKdW23gsCGEs5DqCuTPXwPUye7nxERFm10dHVh8ToRDz36RTaEjEgwv2UT76swWpV16wDqof8V1lrDUMAj2Dlwm9oAkQWnT4DmGbjhPYR7ymNyFqUi39wfHCAh0NTw9TwF00AVmwfldMZcHSYxaPkEFLPN59Rs5NSV64gONHo21eoKSuGlIvxVToCgctCgV9L+tw3CsnFhvrghqv8oHcM+ex2DGCjdC4BZQlAFCYJqxjQHK7ywJP3KN5cQtG6NQpXV2yDQOrKy0lbPdUd/N6usqFmhaWQnszDUqYrs5fHqlS2Cqg20R3WF03Kc/s7+fNrZA+/f8bPC1ysbOFU7h8DjwcRsFZrEwU10FUmgW8vN7DspJT8N5/Qc11CN2sFImgKXgvDhvLXH1hPDeKMCmvxi3/6w4szeDXSj9G7/ruknCPldzx11/8JMAB+f15wDSeU0gAAAABJRU5ErkJggg==',
             'options'   => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAaBJREFUeNqkksFLwmAYxt9vc8MMm0l6qPAYelrgRfDYJehuCB2EwD+kPyDoqJcugdAxAkuCoEN01YunImKiSWVsk1w61/uKrk0nQX7w4/2ejef53u/dmGVZsMjiYMHlc4pMJuN6KcaPgef5BFJCKSNV0zSzSP3saPPvDnpPJ6AoL6WtjW85t7cCVEk3mw3vDnAeeadmxjMo7w05mYiDYRiQTATh9Lwhz53BcDgsDgaDYq/XA6q6uV5kbLlavlOAnlEljXh30O/3Qdd1W2uRQwj5hexDzVd6qKl4slgNhWI4g753QKvVcs2ACRfARfbrgrC0/XvIF8wN0DTNFSBxlxDi74HjOOc1McDE3fVsgKqqroBYLEaDDaNpF+Ua8ob6CuuHc/I2kiRNyAcCAYhGo5BOpw8qlcoNnmpRJY3YHlcH3W530m6TPmmn04F2ux1MpVI79I4q6jJMfXsbe3iMjYKIcDicKxQKt4qiWFRJi6Joe9iUkWER6C8e48eQCJLC/SryifOoIY/oex15PALoDvwYnwNuDBl09OkzAf9ZPwIMALAJ41pzCcUFAAAAAElFTkSuQmCC',
             'valid'     => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAytJREFUeNpMk01ME1EQx//dXdoKaKpFKpBC0iItEsADqIikAmok4ePEQUM5iV6IJhr0oFEvnjyC3jTxYg98FEKiifEDjWhAVLCRr9JCEwQUCWVboFt2t85bDLrJb1923vvPzJvZ0TU0NECn04HjOG0l9hP5ACzEHmw/IvEzkUhMEMsEVFUFWwX8ewzEkZycnPLGxsbzNputUJZlbUMQBAQCAV9nZ+fTUCg0SKZhQmJ7vNPpZFGZ+ExdXV2T2+2+Nih9sniWvGhffIzO5X5Min4IyYLFXX3uFGXKT09PM3GIUHiHw8FSOVFfX99UWVnZfHnmFkZ4HxLpHMyWNJjS9kI0rmMo8hWvl97jYnFzMc/zMjlZZk44RVEs2dnZFdXVVe4r/puIm2WYTCZsbGwgHA5rbG5ubttMEq4G78DlcrmzsrIqmJa32+1HW1outL2Nfkj/qh9HksEAURQRj8d3WF1dxavSLlBkDKwOQr/O4Vh2qWVoaPgzyyDTarUWjIhj2JWcjPn5eS3imriGhcUFVjz05D3S7FVqmWb/EvUhIyOjgGkFepkikSh8sXGkq5n4VP0Cdm8J1uPrVCLgy8lXWidY9Pxnx5GyOwU+fpyCxMC0HLWKZQGFWnbJ7NYijp56A5VTNTFrIcP5sgxyigyJi2lnmYZpmYMIzwvI5/Nw3X9Xi8buPFr+ZkfsGDgGeY8M1ahC4RTtLMAxBxEWfcHvn5k8nFIMRZRQPlG7I2TkvT9KnYlD2aVo6OMcipOLMDs7N8m0zME3j8fjrbGchTWWCV4PFI25NHHuUCliB2I7YuOGgIOyDafNp9HX5/UyLbvCYjA48663t6f7QeFD5P6yQrcmIcdXCCk9hgSvQrepwhgEnCs2tBc8QH9/X/fsbPAd0/J/B+PHxMR3juaDu117z2nZSoN+gabHF4JhDqhKuNB0oBk3Cm/B43nS29X11Eutfk61knVaNQCjwWDYl5qaWulwHDrS2tpWU1JSZv9v0DAy8jHQ0XH/+dTU+HA0Gh2QJGmFzDHmgCdMhJlITUpKMtH97TQ0+/8fZ8pymVIObG1thembfhL8JsJ/BBgAWiSi56vVp4sAAAAASUVORK5CYII=',
             'fail'      => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAjBJREFUeNqkk0trE1EUx8/cO49OfGTSRNJMYsA0aVonoYh13YW71uJCKFQhKqibfgFLwYULsR/AhY+VG1d+C124kJiFIGipmoIZNUXtZDKTycz1njGpaRNU8MJv7txzzv/c5xEYY/A/TRQEAW5c5KwM+aKcR73/a5zvg84HT371wv07Apwuj0x+PZW/vArA4NO7x/f4+OGoIHLKAAiC/fBdHadSbCGZPTeTzC7OUElbQNvBOISMMnIqeqFSYs57mTkfZD1/qYS2f0rAZ5pVDmXnY/FSbn3jM6xvfAEtfjKnRDLz6BtK4PPPADi+ms6vGK71lti2DUintUVSJ84b6OvF7GlI4PNMPVgAZ49oxpyqRnXf+wGWZYX4ngWRiKYfPpqfw5hBjej7eweqCkSo6JOLhmd/hI7vQLVaBdM0YXt1FgK2CeJ40fCbmxUWsGc8vh3egtcFQPhyLsQnzpQJcbVmuw5mawtqtRo0Gg3wJQeY7ALIrqZEM2WM7esIPkROAgR5OZEpTTV3X4IXNEGiLnw1b4fItBNCBQuiqeQUA7qMGtSSLt8C38aVRLo47QVvVJFYoFAnJJG8FdIfI6rSVWMTx6ZRg1rS7UKeSspSMj2Wk+AbjPGZ+vTboA1JZbQcEcUl1Iq2zdZyxURBpruUMTzR38Vl79wM+9bO0/3vlwLVs+OF16/MNdFug/vi+Xadm+vDL/3uHyuR16Er4E3gKvEaOTLa/1LBuEQPF8hxfgowAINnMqTBUH7hAAAAAElFTkSuQmCC');

/**
 * form submited
 */

if(isset($_POST['create'])) {
	
	$errors = array();	
	$form = $_POST;
	
	//checkboxes
	if(!isset($form['public_index'])) $form['public_index'] = 'off';
	if(!isset($form['public_htaccess'])) $form['public_htaccess'] = 'off';
	if(!isset($form['app_bootstrap'])) $form['app_bootstrap'] = 'off';
	if(!isset($form['app_front'])) $form['app_front'] = 'off';
	
	//BEGIN FORM VALIDATION
	//app dir
	if(is_dir($form['app_path'])) { $errors['app_path'] = 'Directory already exists!'; }
	
	//public dir
	if(!empty($form['public_path'])) {
		if(is_dir($form['public_path'])) { $errors['public_path'] = 'Directory already exists!'; }
	}
	
	//controllers
	if(!empty($form['app_controllers'])) {
		$ctrls = explode(',',$form['app_controllers']);
		foreach($ctrls as $ctrl) {
			if((!preg_match('/^[a-zA-Z0-9]*$/',$ctrl)) || ($ctrl === '')) {
				$errors['app_controllers'] = 'Chars allowed for controller(s) title: aA-aZ, 0-9 with no spaces only';
			}
		}
	}
	
	//modules
	if(!empty($form['app_modules'])) {
		$modules = explode(',',$form['app_modules']);
		foreach($modules as $module) {
			if((!preg_match('/^[a-zA-Z0-9]*$/',$module)) || ($module === '')) {
				$errors['app_modules'] = 'Chars allowed for module(s) folder: aA-aZ, 0-9 with no spaces only';
			}
		}
	}
	
	//configs
	if(empty($form['app_configs_file'])) {
		$errors['app_configs_file'] = 'Must specify a configuration file name!';
	}
	elseif(pathinfo($form['app_configs_file'],PATHINFO_EXTENSION) !== 'ini') {
		$errors['app_configs_file'] = 'Only .ini file extension here';
	}
	
	//no errors, start creating app
	//----------------------------------------------
	if(empty($errors)) {
		
		$app_created = false;
		$app_build_errors = array();
		
		include PEAK_PATH.'/Core.php';
		include PEAK_PATH.'/Codegen.php';
		include PEAK_PATH.'/Codegen/Bootstrap.php';
		include PEAK_PATH.'/Codegen/Controller.php';	
		
		//app structure		
		$app_folders = Peak_Core::getDefaultAppPaths($form['app_path']);
		unset($app_folders['views_themes'], $app_folders['theme']);		
		foreach($app_folders as $folder) {
			if (!@mkdir($folder, 0, true)) {
				$app_build_errors[] = 'Failed to create <code>'.$folder.'</code>';
			}
		}
		
		//configs file
		if(!@file_put_contents($form['app_path'].'/'.$form['app_configs_file'], $form['app_configs'])) {
			$app_build_errors[] = 'Failed to create <code>'.$form['app_path'].'/'.$form['app_configs_file'].'</code>';
		}
		
		//bootstrap
		if($form['app_bootstrap'] === 'on') {
			$codegen = new Peak_Codegen_Bootstrap();
			$filepath = $form['app_path'].'/bootstrap.php';
			if(!@file_put_contents($filepath, $codegen->preview())) {
				$app_build_errors[] = 'Failed to create <code>'.$filepath.'</code>';
			}
		}
		
		//controllers
		if(!empty($form['app_controllers'])) {
			$codegen = new Peak_Codegen_Controller();
						
			$ctrls = explode(',',$form['app_controllers']);
			foreach($ctrls as $ctrl) {
				$codegen->name = $ctrl;
				$codegen->add_postaction = true;
				$codegen->add_preaction = true;
				$codegen->actions = array('index');
				$filepath = $form['app_path'].'/Controllers/'.$ctrl.'Controller.php';
				if(!@file_put_contents($filepath, $codegen->preview())) {
					$app_build_errors[] = 'Failed to create <code>'.$filepath.'</code>';
				}
			}
		}
		
		//modules
		if(!empty($form['app_modules'])) {
								
			$mods = explode(',',$form['app_modules']);
			foreach($mods as $mod) {
				
				$modpath = $form['app_path'].'/modules/'.$mod;
				$paths = Peak_Core::getDefaultAppPaths($modpath);
				unset($paths['views_themes'], $paths['theme'], $paths['modules']);
			
				foreach($paths as $folder) {
					if (!@mkdir($folder, 0, true)) {
						$app_build_errors[] = 'Failed to create <code>'.$folder.'</code>';
					}
				}
				
				$codegen = new Peak_Codegen_Bootstrap();
				$filepath = $modpath.'/bootstrap.php';
				$codegen->name = $mod.'_Bootstrap';
				if(!@file_put_contents($filepath, $codegen->preview())) {
					$app_build_errors[] = 'Failed to create <code>'.$filepath.'</code>';
				}
				//print_r($paths);
			}
		}
		
		//creat public path and files
		if(!empty($form['public_path'])) {
			if (!@mkdir($form['public_path'], 0, true)) {
				$app_build_errors[] = 'Failed to create <code>'.$form['public_path'].'</code>';
			}
			if($form['public_index'] === 'on') {
				
			}
			if($form['public_htaccess'] === 'on') {
				$codegen = Peak_Codegen_Htaccess();
				$codegen->env = 'development';
				$filepath = $form['public_path'].'/.htaccess';
				if(!@file_put_contents($filepath, $codegen->preview())) {
					$app_build_errors[] = 'Failed to create <code>'.$filepath.'</code>';
				}
			}
		}
		
		
		//no build errors
		if(empty($app_build_errors)) $app_created = true;
		
	}
	
	
	//echo '<pre style="text-align:left;">'.print_r($form,true).'</pre>';
}

/**
 * Misc functions
 */

function errfield($name, $msg = false)
{
	global $errors;
	if(isset($errors) && isset($errors[$name])) {
		if(!$msg) echo 'class="fail"';
		else echo '<label>&nbsp;</label><small><span class="fail">'.$errors[$name].'</span></small><br />';
	} 
}

/**
 * HTML page
 */
?>
<html>
 <head>
  <title>Create Peak Application</title><meta charset="utf-8" />
  <style type="text/css">
  <!--
  body{ 
   background:#F5F5EA; padding:10px; font:13px "Verdana"; color:#333;
   letter-spacing:0px;  text-align:center;
  }
  input,select,textarea { 
   font:12px "Consolas","Lucida Console"; padding:4px 4px; border:1px solid #ccc; height:25px;
  }
  pre { text-align:left; }
  code { font:12px "Consolas","Lucida Console"; }
  form { line-height:25px; }
  img { vertical-align:text-bottom; }
  ul { list-style-type:square; margin-left: 30px; padding:0; }
  ul li { margin-left:0; padding:0; }
  #wrap { width:900px; margin-right:auto; margin-left:auto; }
  #content { 
   text-align:left; padding:0 10px; background:#F1F1F1; border:1px solid #DBDBDB; border-top:0;
   -moz-border-radius:8px;  -webkit-border-radius:8px;  border-radius:8px;
  }
  .legend {
   background:#F1F1D9; color:#000; margin:0 -11px 0px -11px; padding:5px 12px; 
   font-size:14px; font-weight:bold; border:1px solid #ccc; border-bottom:1px solid #FF7400;
   border-radius:8px 8px 0 0; text-shadow: 2px 2px 3px rgba(200, 200, 200, 1);
  }
  .legend.small {
   font-weight:normal; margin:10px 0 0 0; background:#F1F1D9; border-bottom:1px solid #FF7400;
  }
  .legend.middle { margin-top:150px; }
  .legend.info {
   background:#DBF2C8; font-size:11px; font-weight:normal; color:#000; margin:0 0 10px 0px;
  }
  .legend img { vertical-align:middle; }
  .block {
   padding:20px 15px 15px 15px; margin:0 0 10px 0;
   background:#fff; border:1px solid #ccc;
   border-top:0; line-height:22px;
   -moz-border-radius: 0 0 8px 8px; -webkit-border-radius: 0 0 8px 8px; border-radius: 0 0 8px 8px; 
  }
  .block p { margin:0 0 20px 0; }
  .block p.pass {
   background:#DBF2C8; padding:10px;
   -moz-box-shadow: 1px 1px 2px #ccc; -webkit-box-shadow: 1px 1px 2px #ccc; box-shadow: 1px 1px 2px #ccc;
  }
  form input,form select,form textarea{ width:550px; margin-bottom:0px; background:#F9F9F9; }
  form label { float:left; width:150px; font-weight:bold; }
  form input.right, div.right { float:right; }
  form .errors{color:red;font-size:11px;line-height:20px;margin:0 0 20px 0;}
  .clear { clear:both; height:1px; }  
  .btn {
   background:#e6e6e6; width:100px; cursor:pointer; margin-right:140px;
   -moz-border-radius:4px;  webkit-border-radius:4px; border-radius:4px;
  }
  .btn:hover { background:#F1F1D9; }
  form input.fail { border:1px solid #F21E1E; color:#333; }
  .fail { color: #F21E1E; }
  .ckbox { vertical-align:text-bottom; width:15px; height:15px; border:0; }
  -->
  </style>
 </head>
 <body>
 <div id="wrap">
 
 <?php if($peak_library_found === false) : ?>
 <div id="content">
  <div class="legend middle"><img src="<?php echo $img['fail']; ?>" /> Error !</div>
  <div class="block">
   <p>Application creator can't find Peak Framework library path under <code><?php echo str_replace('\\','/', PEAK_PATH); ?></code><br />
   You have 2 options to resolve this problem:</p>
   <p>
    - Copy <code>/Peak/</code> contents under <code><?php echo str_replace('\\','/', PEAK_PATH); ?></code><br />
    - Or edit constant <code>PEAK_PATH</code> in this file at line 13 to point to peak library path
   </p>
  </div>
 </div>
 <?php exit(); ?>
 <?php endif; ?>
 
 <?php if(isset($app_created) && $app_created === true) : ?>
 <div id="content">
  <div class="legend middle"><img src="<?php echo $img['valid']; ?>" /> Application created!</div>
  <div class="block">
   You have successfully created an application in <code><?php echo $form['app_path']; ?></code><br />
   <?php if(!empty($form['app_path']) && $form['public_index'] === 'on') : ?>
   <a href="#"> Click here to visit your public index</a>
   <?php endif; ?>
  </div>
 </div>
 <?php exit(); ?>
 <?php endif; ?>
 
 <?php if(isset($app_created) && $app_created === false) : ?>
 <div id="content">
  <div class="legend"><img src="<?php echo $img['fail']; ?>" /> Application creation failed!</div>
  <div class="block">
   <p>Can't build application...<br />
   Error(s):</p>
   <p>
    <ul><?php foreach($app_build_errors as $e) echo '<li>'.$e.'</li>'; ?></ul>
   </p>
  </div>
 </div>
 <?php exit(); ?>
 <?php endif; ?>
 
 <div id="content">
 
   <div class="legend"><img src="<?php echo $img['pagetitle']; ?>" /> Peak Framework Application Creator <small>(beta)</small></div>
  
   <div class="block" style="float:right;width:230px;">
    <strong>Requirements</strong><br />
     <ul>
     <li>PHP <?php echo PHP_VERSION; ?> <img src="<?php echo ($php_version) ? $img['valid'] : $img['fail']; ?>" /></li>
     <?php if($apache_mod_rewrite >= 1) {    	
     	echo '<li>Apache mod_rewrite <img src="'.(($apache_mod_rewrite == 2) ? $img['valid'] : $img['fail']).'" /></li>';
     }
     ?>
     </ul>
   </div>
   
   <div class="block" style="margin:0 270px 0 0;">
    <p><small>
    This tools will help you to create application directory structure and save time. This file should be deleted and not used in production environment.
    If its the first time you create a Peak application and you are not so sure about of settings below, just edit 'path' and leave others unchanged. 
    Peak applications are flexible and can be constructed in several ways. This tools will only help you to start new application in its basic form.</small>
    </p>
   </div>
   
   <div class="clear"></div>
   
   <div class="legend small"><img src="<?php echo $img['newapp']; ?>" /> New application 
   <?php if(isset($errors) && !empty($errors)) echo ' - <span class="fail">The form contains errors!</span>'; ?></div>
     
   <div class="block">
   
   <div class="right"><small>* = optionnal</small></div>
   
   <form method="POST" action="">

    <p>
     <?php errfield('app_path',true); ?> 
	 <label><img src="<?php echo $img['folder']; ?>" /> Path:</label>
     <input name="app_path" type="text" value="<?php echo $form['app_path']; ?>" <?php errfield('app_path'); ?> /><br />
     <label>&nbsp;</label><small>Path where your application will be created.</small><br />
    </p>
            
    <p>
     <?php errfield('app_configs_file',true); ?>
	 <label><img src="<?php echo $img['config']; ?>" /> 
	 Configurations:</label>
	 <input name="app_configs_file" type="text" value="<?php echo $form['app_configs_file']; ?>" style="width:200px;" <?php errfield('app_configs_file'); ?> /><br />
	</p>
	<p>
	 <label>&nbsp;</label>
     <textarea name="app_configs" style="width:550px;height:300px;" spellcheck="false"><?php echo $form['app_configs']; ?></textarea>   
    </p>
    
    <p>
     <?php errfield('public_path',true); ?>
     <label><img src="<?php echo $img['folder']; ?>" /> Public Path:</label>
     <input type="text" name="public_path" value="<?php echo $form['public_path']; ?>" <?php errfield('public_path'); ?> /> *<br />
     <label>&nbsp;</label><small>Path where your public folder will be created. Leave empty to skip.</small><br />
     <label>&nbsp;</label>Create: <input class="ckbox" name="public_index" <?php if($form['public_index'] === 'on') echo 'checked="checked"'; ?>type="checkbox" /> index.php 
     <input class="ckbox" name="public_htaccess" <?php if($form['public_htaccess'] === 'on') echo 'checked="checked"'; ?> type="checkbox" /> .htaccess 
    </p>

    <p>
     <?php errfield('app_controllers',true); ?>
	 <label><img src="<?php echo $img['files']; ?>" /> Controller(s):</label>
     <input name="app_controllers" type="text" value="<?php echo $form['app_controllers']; ?>" <?php errfield('app_controllers'); ?> /> *<br />
     <label>&nbsp;</label><small>Separate controllers title with comma (ex: index,error,contact,about,...)</small><br />
    </p>
    
    <p>
     <?php errfield('app_modules',true); ?>
	 <label><img src="<?php echo $img['folders']; ?>" /> Module(s):</label>
     <input name="app_modules" type="text" value="<?php echo $form['app_modules']; ?>"  <?php errfield('app_modules'); ?> /> *<br />
     <label>&nbsp;</label><small>Separate modules folders with comma (ex: admin,blog,...)</small>
    </p>
    
    <p>
     <label><img src="<?php echo $img['options']; ?>" /> Other options:</label>
     <input class="ckbox" name="app_bootstrap" <?php if($form['app_bootstrap'] === 'on') echo 'checked="checked"'; ?> type="checkbox" /> Bootstrap *<br />
     <label>&nbsp;</label>
     <input class="ckbox" name="app_front" <?php if($form['app_front'] === 'on') echo 'checked="checked"'; ?> type="checkbox" /> Front Controller *
    </p>
     
    <input name="create" class="btn right" type="submit" value="Create" />
    <div class="clear"></div>
     
   </form>
    
   </div><!-- /block -->
   
 </div><!-- /content -->
 
 </div><!-- /wrap -->  
 </body>
</html>