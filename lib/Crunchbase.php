<?php

// Crunchbase PHP SDK
// Author: Justin Heisler
// Date: Feb 14 / 2014

define('CRUNCHBASE_TRANSPORT_PROTOCOL', 'http');
define('CRUNCHBASE_HOST', 'api.crunchbase.com/v/1/');
if(!defined('CRUNCHBASE_DEBUG')){
	define('CRUNCHBASE_DEBUG', false);
}

class Crunchbase {

	public $route = '/';

	function debug($message, $level = E_USER_NOTICE){
		if(!CRUNCHBASE_DEBUG){
			return false;
		} 
		$message = "[crunchbase]: $message";
    	trigger_error($message, $level);
	}

	function generate_url($route){
		if(strpos($route, 'posts') !== FALSE){
			$route = $route.'?api_key='.CRUNCHBASE_API_KEY;
		} else {
			$route = $route.'.js?api_key='.CRUNCHBASE_API_KEY;
		}
		return 
			CRUNCHBASE_TRANSPORT_PROTOCOL.'://'.
			CRUNCHBASE_HOST.$route;
	}

	function validate_response($body){

		if(!$body){
			return false;
		} else {
			if(is_object($body)){
				$body = json_decode($body);
			}
			return $body;
		}
	}

	function exec($url){
		if(!defined('CRUNCHBASE_API_KEY')){
			$this->debug('CRUNCHBASE Api key not defined');
			return false;
		}

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

		$response 		= curl_exec($ch);
		$error_number 	= curl_errno($ch);
		$error_message 	= curl_error($ch);

		if($error_number){
			$this->debug("Curl encountered and error. Code: $error_number. Message: $error_message", E_USER_WARNING);
			return false;
		}

		return $this->validate_response($response);
	}

	function send($url){
		return $this->exec($url, false);
	}

	function valid_entity($entity){
		if(!in_array($entity, array('company', 'person', 'financial-organization', 'product', 'service-provider', 
			'people', 'companies', 'products', 'financial-organizations'))){
			return false;
		}
		return true;
	}

	function valid_query($query){
	    if(!$query || !is_object($query)){
	      $this->debug('Tried creating object but no data given', E_USER_WARNING);
	      return false;
	    }
	    return true;
	}

	function entity_info($query){
		if($this->valid_query($query)){
			if($this->valid_entity($query->entity)){
				$this->route = urlencode($query->entity).'/'.urlencode($query->name);
				$url = $this->generate_url($this->route);
				return $this->send($url);
			}
			$this->debug('Invalid entity', E_USER_WARNING);
		}
		$this->debug('Invalid query', E_USER_WARNING);
	}

	function entity_list($query){
		if($this->valid_query($query)){
			if($this->valid_entity($query->entity)){
				$this->route = urlencode($query->entity);
				$url = $this->generate_url($this->route);
				return $this->send($url);
			}
			$this->debug('Invalid entity', E_USER_WARNING);
		}
		$this->debug('Invalid query', E_USER_WARNING);
	}

	function search($query){
		if($this->valid_query($query)){
			$this->route = 'search';
			$url = $this->generate_url($this->route);
			$url = $url.'&query='.$query->query;
			return $this->send($url);
		}
		$this->debug('Invalid query', E_USER_WARNING);
	}

	function posts($query){
		if($this->valid_query($query)){
			if($this->valid_entity($query->entity)){
				$this->route = urlencode($query->entity).'/posts';
				$url = $this->generate_url($this->route);
				$url = $url.'&name='.$query->name.
				'&first_name='.$query->first_name.
				'&last_name='.$query->last_name;
				return $this->send($url);
			}
			$this->debug('Invalid entity', E_USER_WARNING);
		}
		$this->debug('Invalid query', E_USER_WARNING);
	}
}

?>