<?php

function smarty_function_jdecode($params,$template) {
	
	reset($params);
	$first_key = key($params);
	
	//we need an associative array
	if(is_int($first_key)) return false;
	
	$object = json_decode($params[$first_key]);
	
	if(isset($object->_fields) && is_object($object->_fields)){
		
		$fields = array();
		
		foreach ($object->_fields as $field => $json) {
			$fields[$field] = json_decode($json);
		}
	
		unset($object->_fields);
	}
	
	if(isset($fields)) $object->_fields = $fields;
	
	$template->assign($first_key, $object);

}