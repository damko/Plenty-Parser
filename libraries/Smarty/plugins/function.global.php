<?php
/**
 * Smarty plugin
 *
 * @package Smarty
 * @subpackage PluginsFunction
 */

function smarty_function_global($params, &$smarty) { 
  static $globals; 

  if (isset($params['put'])) {
  	$globals[$params['put']] = $params['value'];
  }
  
  if(isset($params['get'])) {
  	if(isset($globals[$params['get']])) {
  		return $globals[$params['get']];
  	} else {
  		return false;
  	}
  }

  return true;
}

?>