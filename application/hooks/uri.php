<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
function rewrite_base_url()
{
	$n = count(explode('/', uri_string()))-2 ;
	$str = '';
	for ($i=0; $i < $n; $i++)
	{ 
		$str .= '../';
	}
 
	$CI =& get_instance();
	$CI->config->set_item('base_url', $str);
}
 